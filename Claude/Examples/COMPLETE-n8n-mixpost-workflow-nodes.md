# Complete n8n to Mixpost Workflow - All Node Updates

This file contains all the necessary code updates for your n8n workflow to publish from Airtable to Mixpost.

## 1. Prepare Mixpost Data Node (Code Node)

This node extracts data from the Job Role node and prepares it for processing.

```javascript
// Get data from Job Role node using node reference
const data = $('Job Role').item.json;
const airtableRecord = data.airtable_record || data;

// Extract content fields
const content = airtableRecord.Description || airtableRecord.content || '';

// Handle the title field - check for array values
let title = airtableRecord.Title || '';
if (!title && airtableRecord['Description (from Coaching Call)']) {
  const descFromCall = airtableRecord['Description (from Coaching Call)'];
  title = Array.isArray(descFromCall) ? descFromCall[0] : descFromCall;
}

// Handle media attachments - check multiple possible field names
const imageAttachments = airtableRecord['📥 Image'] || [];
let videoAttachments = airtableRecord['📥 Video'] || [];

// Check alternative video fields if main field is empty
if (videoAttachments.length === 0) {
  videoAttachments = airtableRecord['Final Clip (watch me)'] || 
                     airtableRecord['Cropped Clip'] || [];
}

// Extract publish date/time - it comes as UTC from Airtable
let publishDate = airtableRecord['Publish Date'];
if (!publishDate) {
  publishDate = airtableRecord['Publish Date/Time'];
}

const publishNow = airtableRecord['Publish Now'] === true;

// Extract platforms - may need to be set manually or from a different field
const platforms = airtableRecord.Platforms || ['Facebook', 'Instagram'];

// Extract URLs from Airtable attachments
const imageUrls = imageAttachments.map(att => att.url || att);
const videoUrls = videoAttachments.map(att => att.url || att);

// For clips, we might also have direct URLs
if (airtableRecord['Clip URL'] && videoUrls.length === 0) {
  videoUrls.push(airtableRecord['Clip URL']);
}

// Determine scheduling and if it should be a story
let scheduleData = {};
let shouldBeStory = false;

if (publishNow) {
  scheduleData = {
    schedule_now: true,
    schedule: false,
    queue: false
  };
} else if (publishDate) {
  // Airtable provides UTC time (ends with Z)
  const utcDate = new Date(publishDate);
  
  // Create a formatter for Central Time
  const centralTimeStr = utcDate.toLocaleString("en-US", {
    timeZone: "America/Chicago",
    year: 'numeric',
    month: '2-digit',
    day: '2-digit',
    hour: '2-digit',
    minute: '2-digit',
    hour12: false
  });
  
  // Parse the formatted string
  const [datePart, timePart] = centralTimeStr.split(', ');
  const [month, day, year] = datePart.split('/');
  const [hours, minutes] = timePart.split(':');
  
  scheduleData = {
    date: `${year}-${month.padStart(2, '0')}-${day.padStart(2, '0')}`,
    time: `${hours}:${minutes}`,
    timezone: 'America/Chicago', // CDT/CST
    schedule: true,
    schedule_now: false,
    queue: false
  };
} else {
  // Add to queue if no date - and mark as potential story for FB/IG (requires media)
  scheduleData = {
    queue: true,
    schedule: false,
    schedule_now: false
  };
  shouldBeStory = true; // Note: Stories require media, text-only posts will remain as regular posts
}

// Prepare media info for processing
const mediaInfo = {
  images: imageUrls,
  videos: videoUrls,
  hasMedia: imageUrls.length > 0 || videoUrls.length > 0
};

// Get base and table IDs from the webhook data if available
let baseId = airtableRecord.baseId;
if (!baseId && data.webhook_body && data.webhook_body.variables) {
  baseId = data.webhook_body.variables.BASE_ID;
}

let tableId = airtableRecord.tableId;
if (!tableId && data.webhook_body && data.webhook_body.variables) {
  tableId = data.webhook_body.variables.CLIPS_TABLE || 
           data.webhook_body.variables.CONTENT_TABLE;
}

// Handle clip metadata arrays
const clipAspectRatio = airtableRecord['Clip Aspect Ratio (from Videos)'];
const clipWidth = airtableRecord['Clip x-Width (from Videos)'];
const clipHeight = airtableRecord['Clip y-Height (from Videos)'];

return {
  content,
  title,
  mediaInfo,
  platforms,
  shouldBeStory,
  ...scheduleData,
  recordId: airtableRecord.id || airtableRecord.recordId,
  baseId,
  tableId,
  rawRecord: airtableRecord,
  clipMetadata: {
    duration: airtableRecord.Duration,
    aspectRatio: Array.isArray(clipAspectRatio) ? clipAspectRatio[0] : clipAspectRatio,
    width: Array.isArray(clipWidth) ? clipWidth[0] : clipWidth,
    height: Array.isArray(clipHeight) ? clipHeight[0] : clipHeight
  }
};
```

## 2. Prepare Media Uploads Node (Code Node)

This node prepares media items for individual upload processing.

```javascript
const preparedData = $('Prepare Mixpost Data').item.json;
const mediaInfo = preparedData.mediaInfo;
const uploadPromises = [];

// Prepare all media items for upload
const allMedia = [];

// Add images
mediaInfo.images.forEach((url, index) => {
  allMedia.push({
    url,
    type: 'image',
    index
  });
});

// Add videos
mediaInfo.videos.forEach((url, index) => {
  allMedia.push({
    url,
    type: 'video',
    index
  });
});

// Return items for individual processing
return allMedia.map(media => ({
  json: {
    mediaUrl: media.url,
    mediaType: media.type,
    mediaIndex: media.index,
    originalData: preparedData
  }
}));
```

## 3. Process Media & Determine Post Type Node (Code Node)

This node processes uploaded media and determines the post type (reel, video, image, text).

```javascript
// Aggregate uploaded media results
const uploadedMedia = $input.all();
const preparedData = $('Prepare Mixpost Data').item.json;
const accounts = $('Get Mixpost Accounts').item.json;

// Extract account IDs based on platform names
const platformMapping = {
  'Facebook': 'facebook_page',
  'Instagram': 'instagram',
  'YouTube': 'youtube',
  'Twitter': 'twitter',
  'X': 'twitter'
};

// Filter accounts based on selected platforms
const selectedAccountIds = [];
if (preparedData.platforms && preparedData.platforms.length > 0) {
  accounts.data.forEach(account => {
    const platformKey = Object.keys(platformMapping).find(key => 
      preparedData.platforms.includes(key) && 
      account.provider === platformMapping[key]
    );
    if (platformKey) {
      selectedAccountIds.push(account.id);
    }
  });
} else {
  // If no platforms specified, use all accounts
  selectedAccountIds.push(...accounts.data.map(acc => acc.id));
}

// Process uploaded media and determine post types
const mediaIds = [];
const videoMediaIds = [];
let postType = 'post'; // default
let hasVerticalVideo = false;
let videoTooLongForReel = false;

uploadedMedia.forEach(item => {
  // The media ID is at the top level of the response
  if (item.json && item.json.id) {
    const media = item.json;
    mediaIds.push(media.id);
    
    // Check if it's a video
    if (media.type === 'video' || media.is_video) {
      videoMediaIds.push(media.id);
      
      // For vertical video detection, we'll use the metadata from prepared data
      const clipMetadata = preparedData.clipMetadata;
      if (clipMetadata && clipMetadata.aspectRatio && clipMetadata.aspectRatio < 1) {
        hasVerticalVideo = true;
        
        // Check duration for reel eligibility (max 90 seconds)
        if (clipMetadata.duration && clipMetadata.duration > 90) {
          videoTooLongForReel = true;
        }
      }
    }
  }
});

// Check if this should be a story (no publish date + has media)
if (preparedData.shouldBeStory && mediaIds.length > 0) {
  postType = 'story';
} else {
  // Determine post type based on media
  if (hasVerticalVideo && !videoTooLongForReel) {
    postType = 'reel';
  } else if (videoMediaIds.length > 0) {
    postType = 'video';
  } else if (mediaIds.length > 0) {
    postType = 'image';
  }
}

// Build platform-specific options
const platformOptions = {};

if (preparedData.platforms.includes('Facebook')) {
  if (postType === 'story') {
    platformOptions.facebook_page = { type: 'story' };
  } else if (postType === 'reel') {
    platformOptions.facebook_page = { type: 'reel' };
  } else {
    platformOptions.facebook_page = { type: 'post' };
  }
}

if (preparedData.platforms.includes('Instagram')) {
  if (postType === 'story') {
    platformOptions.instagram = { type: 'story' };
  } else if (postType === 'reel') {
    platformOptions.instagram = { type: 'reel' };
  } else {
    platformOptions.instagram = { type: 'post' };
  }
}

if (preparedData.platforms.includes('YouTube') && videoMediaIds.length > 0) {
  platformOptions.youtube = {
    title: preparedData.title || preparedData.content.substring(0, 100),
    status: 'public'
  };
}

return {
  mediaIds,
  videoMediaIds,
  selectedAccountIds,
  postType,
  platformOptions,
  preparedData
};
```

## 4. Process Without Media Node (Code Node)

For posts without media.

```javascript
// Get data for posts without media
const preparedData = $('Prepare Mixpost Data').item.json;
const accounts = $('Get Mixpost Accounts').item.json;

// Extract account IDs based on platform names
const platformMapping = {
  'Facebook': 'facebook_page',
  'Instagram': 'instagram',
  'YouTube': 'youtube',
  'Twitter': 'twitter',
  'X': 'twitter'
};

// Filter accounts based on selected platforms
const selectedAccountIds = [];
if (preparedData.platforms && preparedData.platforms.length > 0) {
  accounts.data.forEach(account => {
    const platformKey = Object.keys(platformMapping).find(key => 
      preparedData.platforms.includes(key) && 
      account.provider === platformMapping[key]
    );
    if (platformKey) {
      selectedAccountIds.push(account.id);
    }
  });
} else {
  // If no platforms specified, use all accounts
  selectedAccountIds.push(...accounts.data.map(acc => acc.id));
}

// Text-only posts cannot be stories, so use default post type
return {
  mediaIds: [],
  videoMediaIds: [],
  selectedAccountIds,
  postType: 'text',
  platformOptions: {},
  preparedData
};
```

## 5. Prepare Post Payload Node (Code Node)

Add this node after removing the Merge node. Connect both processing paths to this node.

```javascript
// Get data from either path
const data = $input.first().json;

// Handle data from either path - check if preparedData exists at different levels
const pd = data.preparedData || data;

// Get account IDs - might be at different levels
const accountIds = data.selectedAccountIds || [];

// Get media IDs - will be empty array if no media
const mediaIds = data.mediaIds || [];

// Get platform options - will be empty object if no special options
const platformOptions = data.platformOptions || {};

const payload = {
  accounts: accountIds,
  versions: [
    {
      account_id: 0,
      is_original: true,
      content: [
        {
          body: pd.content || '',
          media: mediaIds
        }
      ],
      options: platformOptions
    }
  ]
};

// Add scheduling fields only if they're needed
if (pd.date) payload.date = pd.date;
if (pd.time) payload.time = pd.time;
if (pd.timezone) payload.timezone = pd.timezone;
if (pd.schedule !== undefined) payload.schedule = pd.schedule;
if (pd.schedule_now !== undefined) payload.schedule_now = pd.schedule_now;
if (pd.queue !== undefined) payload.queue = pd.queue;

return {
  json: {
    payload: payload,
    payloadString: JSON.stringify(payload)
  }
};
```

## 6. Create Mixpost Post Node (HTTP Request)

**Configuration:**
- Method: POST
- URL: `https://social.everydaycreator.org/mixpost/api/dd7477ef-bcd2-45dd-8f2e-14e790fdd2e2/posts`
- Headers:
  - Authorization: `Bearer YwNO7dcJiIs7o0r9hwgqg8HoamYjIZoh0pLmpz8y67681cb9`
  - Accept: `application/json`
  - Content-Type: `application/json`
- Body Content Type: JSON
- JSON Body: `{{ $json.payloadString }}`

## Workflow Connections

1. Job Role → Prepare Mixpost Data
2. Prepare Mixpost Data → Get Mixpost Accounts
3. Get Mixpost Accounts → Has Media? (IF node)
4. Has Media? (true) → Prepare Media Uploads → Upload Media to Mixpost → Process Media & Determine Post Type
5. Has Media? (false) → Process Without Media
6. Both processing nodes → Prepare Post Payload
7. Prepare Post Payload → Create Mixpost Post
8. Create Mixpost Post → Update Airtable Status

## Important Notes

1. **Remove the Merge node** - It blocks execution
2. **Timezone**: Using UTC since Airtable provides UTC timestamps
3. **Media Upload**: The existing HTTP Request node for upload works fine - just make sure Body Content Type is set to "Form-Data"
4. **Platforms**: Update the default platforms array in Prepare Mixpost Data if needed