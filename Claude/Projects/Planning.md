# Mixpost Comments API & Webhooks Implementation Plan

## Overview
Understanding and utilizing the existing comment and webhook functionality in Mixpost Pro for AI-driven content updates via n8n workflows.

## Current State Analysis (UPDATED with Pro Version)

### Existing Infrastructure in Pro Version
1. **Frontend Comments UI**: Fully implemented in Pro version
   - Comment display component: `ItemCommentType.vue`
   - New comment input: `NewComment.vue`
   - Activity tab with full functionality

2. **Database**: Complete schema exists
   - `mixpost_post_activities` - Stores all post activities including comments (text field)
   - `mixpost_post_activity_reactions` - Reactions to activities
   - `mixpost_webhooks` - Webhook configurations
   - `mixpost_webhook_deliveries` - Webhook delivery history

3. **API Structure**: Full API implementation exists
   - API routes in `/routes/api/api.php` and `/routes/api/includes/workspace.php`
   - Controllers for comments: `PostCommentsController`, `PostActivitiesController`
   - Webhook controllers in both Admin and Workspace contexts
   - API authentication via tokens (Features::isApiAccessTokenEnabled())

4. **Webhook System**: Complete implementation
   - Events already defined: AccountAdded, AccountDeleted, PostCreated, PostPublished, etc.
   - Webhook delivery with retry logic
   - Webhook secret for signature verification

## What We Need to Do

Since Mixpost Pro already has complete webhook and comment functionality, we need to:

### 1. Enable API Access
- Check if API access tokens are enabled in the system
- Generate API tokens for authentication
- Configure API middleware

### 2. Find/Add Comment Webhook Events
The system already has these webhook events:
- `PostActivityCreated` - When any activity (including comments) is created
- `PostCommentUpdated` - When a comment is updated
- `PostCommentDeleted` - When a comment is deleted
- `PostCommentReactionsUpdated` - When reactions change

### 3. Existing API Endpoints (Need to verify/test)
Based on the controllers found:

#### Post Activities/Comments API
- `GET /api/{workspace}/posts/{post}/activities` - List all activities for a post
- `POST /api/{workspace}/posts/{post}/comments` - Create new comment
- `PUT /api/{workspace}/posts/{post}/comments/{comment}` - Update comment
- `DELETE /api/{workspace}/posts/{post}/comments/{comment}` - Delete comment
- `GET /api/{workspace}/posts/{post}/comments/{comment}/children` - Get comment replies
- `POST /api/{workspace}/posts/{post}/comments/{comment}/reactions` - React to comment

#### Posts API (for AI updates)
- `GET /api/{workspace}/posts` - List posts
- `POST /api/{workspace}/posts` - Create post
- `GET /api/{workspace}/posts/{post}` - Get single post
- `PUT /api/{workspace}/posts/{post}` - Update post
- `DELETE /api/{workspace}/posts/{post}` - Delete post
- `POST /api/{workspace}/posts/schedule/{post}` - Schedule post

### 4. Configure Webhooks
Access webhook settings (likely in admin panel) to:
- Create webhook for `PostActivityCreated` event
- Set n8n webhook URL
- Configure authentication/secret

### 5. n8n Integration Example
The webhook payload for PostActivityCreated will likely be:
```json
{
  "event": "post.activity.created",
  "timestamp": "2024-01-15T10:30:00Z",
  "workspace": {
    "id": 1,
    "name": "My Workspace"
  },
  "data": {
    "activity": {
      "id": "uuid",
      "post_id": 123,
      "user_id": 1,
      "type": 2, // comment type
      "text": "Please update the caption",
      "created_at": "2024-01-15T10:30:00Z"
    },
    "post": {
      "id": "uuid",
      "status": "scheduled",
      "scheduled_at": "2024-01-20T15:00:00Z"
    },
    "user": {
      "id": 1,
      "name": "Client Name"
    }
  }
}
```

## API Usage Examples

### Authentication
```bash
# API token should be included in Authorization header
curl -H "Authorization: Bearer YOUR_API_TOKEN" \
  https://mixpost.example.com/api/{workspace}/posts/{post}/activities
```

### Create Comment
```bash
curl -X POST https://mixpost.example.com/api/{workspace}/posts/{post}/comments \
  -H "Authorization: Bearer YOUR_API_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "text": "Please update the hashtags",
    "parent_id": null
  }'
```

### Update Post (for AI agent)
```bash
curl -X PUT https://mixpost.example.com/api/{workspace}/posts/{post} \
  -H "Authorization: Bearer YOUR_API_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "accounts": [1, 2, 3],
    "versions": [{
      "account_id": 0,
      "is_original": true,
      "content": [{
        "body": "Updated content here",
        "media": []
      }]
    }],
    "date": "2024-01-20",
    "time": "15:00"
  }'
```

## Testing Steps

1. **Access Your VPS Mixpost Instance**
   - Navigate to https://your-vps-ip:9000
   - Log in as admin

2. **Enable API Access & Generate Token**
   - Go to Settings > API Access (if available)
   - Or check database for API token configuration

3. **Configure Webhooks**
   - Navigate to Settings > Webhooks or Admin > Webhooks
   - Create new webhook with:
     - URL: Your n8n webhook URL
     - Events: Select PostActivityCreated
     - Active: Yes

4. **Test Comment Creation**
   - Create a comment on a post via UI
   - Check if webhook is triggered in n8n

## Next Steps
1. Export database from VPS to understand data structure
2. Test API endpoints with actual workspace/post IDs
3. Document the exact webhook payload format
4. Create n8n workflow template for AI content updates