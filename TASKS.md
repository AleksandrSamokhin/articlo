## Tasks

- ~~Refactor the `Dashboard/PostController.php` store and update methods. Move the validation and temporary file storage logic into a PostService class instead.~~ ✅

## Progress

### Refactor PostController to use PostService (Completed)

Refactored the `Dashboard/PostController.php` `store` and `update` methods to use a new `PostService` class.

**Changes made:**

1. **Created `app/Services/PostService.php`** with the following methods:
   - `preparePostData()` - Prepares post data for creation (adds user_id, generates slug, removes categories/image keys)
   - `preparePostDataForUpdate()` - Prepares post data for update (regenerates slug only if title changed)
   - `handleTemporaryFileUpload()` - Handles temporary file upload and attaches media to post using Spatie Media Library
   - `hasTemporaryFile()` - Helper method to check if a temporary file exists

2. **Refactored `PostController.php`:**
   - Injected `PostService` via constructor dependency injection
   - `store()` method: Reduced from ~50 lines to ~15 lines by using service methods
   - `update()` method: Reduced from ~60 lines to ~25 lines by using service methods

3. **Added unit tests in `tests/Unit/Services/PostServiceTest.php`:**
   - Tests for `preparePostData()` (user_id, slug generation, category/image removal)
   - Tests for `preparePostDataForUpdate()` (slug handling, data cleanup)
   - Tests for `hasTemporaryFile()` (null handling, existence checks)
   - Tests for `handleTemporaryFileUpload()` (null and missing file handling)