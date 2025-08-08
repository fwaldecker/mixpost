# Story Workflow Update Guide

## Overview
This update modifies the n8n workflow to automatically create Story posts for Facebook and Instagram when no publish date/time is provided in Airtable.

## Key Changes

### 1. Prepare Mixpost Data Node
Added a `shouldBeStory` flag that is set to `true` when:
- `Publish Now` is NOT checked
- `Publish Date` is empty
- The post will be added to the queue

```javascript
let shouldBeStory = false;

if (publishNow) {
  // ... regular immediate post logic
} else if (publishDate) {
  // ... regular scheduled post logic
} else {
  // Add to queue if no date - and mark as story for FB/IG
  scheduleData = {
    queue: true,
    schedule: false,
    schedule_now: false
  };
  shouldBeStory = true;
}
```

### 2. Process Media & Determine Post Type Node
Updated to check for the `shouldBeStory` flag first:
- If `shouldBeStory` is true, sets post type to 'story'
- Otherwise, uses existing logic (reel for vertical videos, etc.)
- Sets platform-specific options with `type: 'story'` for Facebook and Instagram

```javascript
// Check if this should be a story (no publish date)
if (preparedData.shouldBeStory) {
  postType = 'story';
} else {
  // Existing logic for determining post type based on media
}

// Platform options now include story type
if (postType === 'story') {
  platformOptions.facebook_page = { type: 'story' };
  platformOptions.instagram = { type: 'story' };
}
```

### 3. Process Without Media Node
Also updated to handle Story posts for text-only content:
- Checks the `shouldBeStory` flag
- Sets appropriate platform options for stories

## How It Works

1. **Regular Post**: Set a publish date/time → Creates a scheduled post
2. **Immediate Post**: Check "Publish Now" → Publishes immediately
3. **Story Post**: Leave both empty → Queues as a Story for FB/Instagram

## Benefits

- Clients can easily create multiple stories throughout the day
- No need to manually select "Story" type for each post
- Stories are queued, allowing batch scheduling later
- Works with both media posts and text-only posts

## Installation

1. Replace your existing workflow nodes with the updated version in `n8n-mixpost-publisher-nodes-with-story.json`
2. No configuration changes needed - the logic is automatic
3. Test with a few posts to ensure proper behavior

## Video Length Notes

- Instagram Stories: Max 60 seconds
- Facebook Stories: Max 120 seconds (sometimes limited to 26 seconds)
- Videos longer than these limits will be automatically trimmed by the platforms