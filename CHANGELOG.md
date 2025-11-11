# Changelog

All notable changes to the Tutor-IA plugin (local_dttutor) will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

Nothing yet.

## [1.0.0] - 2025-10-07

### Added

#### Core Functionality
- **Floating chat drawer**: Fixed drawer that redistributes page space (based on aiplacement_courseassist pattern)
- **Floating toggle button**: Avatar button in bottom corner (right/left configurable)
- **Real-time streaming**: Server-Sent Events (SSE) for token-by-token AI responses
- **Session management**: Intelligent caching with TTL validation
- **Role detection**: Automatically detects user role (Teacher/Student) in course context
- **Course context filtering**: Only shows in course and module pages (not frontpage)

#### Web Services
- `local_dttutor_create_chat_message`: Create chat message and get stream URL
- `local_dttutor_delete_chat_session`: Delete chat session

#### HTTP Client
- `tutoria_api` class for external API communication
- Session caching with configurable TTL
- Automatic session validation and refresh
- cURL-based HTTP requests with error handling

#### Hook System
- `before_footer_html_generation` hook for injecting drawer and toggle HTML
- Context-aware rendering (course/module only)
- Integration with Moodle's message drawer (auto-close on conflict)

#### User Interface
- **Drawer**: Header with avatar and close button, scrollable messages area, input footer
- **Messages**: User messages (blue bubbles, right-aligned), AI messages (white bubbles, left-aligned)
- **Typing indicator**: Animated dots while AI is processing
- **Auto-scroll**: Automatic scroll to bottom as tokens arrive
- **Keyboard shortcuts**: Enter to send, Shift+Enter for newline, Escape to close
- **Responsive**: Adapts to different screen sizes

#### Configuration
- **Enable/Disable**: Global toggle for chat functionality
- **Avatar selection**: 10 predefined avatars to choose from
- **Avatar position**: Right or left corner placement

#### Assets
- 9 avatar images (avatar_profesor_01.png through avatar_profesor_10.png)
- Complete CSS styling (~250 lines)
- AMD JavaScript module with full drawer management

#### Documentation
- Comprehensive README.md with installation, configuration, usage, API docs
- CLAUDE.md for Claude Code integration with development workflows
- CHANGELOG.md (this file)
- Inline code documentation (PHPDoc, JSDoc)

#### Developer Tools
- Migration script (`cli/migrate_from_datacurso.php`) for upgrading from local_datacurso
- Settings page with all configuration options
- Cache definitions for session storage
- Capability system (`local/dttutor:use`)

#### Internationalization
- English language strings (lang/en/local_dttutor.php)
- Spanish language strings (lang/es/local_dttutor.php)
- All UI elements translatable

#### Accessibility
- ARIA labels on all interactive elements
- Keyboard navigation support
- Screen reader compatible
- Focus management (jump-to functionality)
- Tab index management for drawer

### Fixed

#### SSE Stream Handling
- **Issue**: "[Conexión interrumpida]" message appearing after every completed message
- **Cause**: EventSource 'error' event fires on normal connection close
- **Solution**: Implemented `messageCompleted` flag that tracks 'done' event before showing errors
- **Result**: Error message only shows on actual connection interruptions, not normal completions

#### Footer Popover Positioning
- **Issue**: Moodle's footer-popover buttons (message drawer, communication) not moving when Tutor-IA drawer opens
- **Cause**: Moodle uses fixed width (315px) in compiled SCSS, Tutor-IA drawer is 380px
- **Solution**: Added body class (`tutor-ia-drawer-open-right/left`) and CSS overrides with !important
- **Result**: Footer buttons now move correctly with drawer width (calc(380px + 2rem))

#### Drawer Width Consistency
- **Issue**: Drawer width mismatch between drawer element and footer-popover repositioning
- **Cause**: Hard-coded values in different places
- **Solution**: Standardized to 380px with clear documentation in CSS comments
- **Result**: Consistent spacing and movement across all elements

### Changed

#### Namespace Migration
- **From**: `local_datacurso` (embedded Tutor-IA)
- **To**: `local_dttutor` (standalone plugin)
- **Reason**: Separation of concerns - decouple Tutor-IA from course/activity AI generation
- **Impact**: All PHP namespaces, web services, config keys, and string keys updated

#### Config Key Simplification
- `enablechat` → `enabled` (standard naming)
- `tutoria_avatar` → `avatar` (no ambiguity in dedicated plugin)
- `tutoria_avatar_position` → `avatar_position` (simpler)

#### String Key Simplification
- `tutoruia` → `pluginname` (Moodle standard)
- `opentutoria` → `open` (simpler)
- `closetutoria` → `close` (simpler)
- Added error strings with proper formatting placeholders

#### Hook Implementation
- **Removed**: AI course/activity button injection (belongs in local_datacurso)
- **Removed**: Course session checking (belongs in local_datacurso)
- **Kept**: Only Tutor-IA chat functionality
- **Result**: Cleaner, focused hook with single responsibility

### Removed

- **Dependencies on datacurso classes**: No longer uses ai_course, streaming_helper, or other datacurso-specific classes
- **Activity/Course AI buttons**: Not part of Tutor-IA functionality
- **Legacy code**: Removed unused tutor_ia_drawer.js and tutor_ia_trigger.mustache references

### Security

- **Input sanitization**: All user input sanitized before sending to API (sanitizeString method)
- **Message length limits**: 4000 characters per user message, 10000 characters per AI response
- **Capability checks**: `local/dttutor:use` capability required for all web services
- **Token authentication**: Bearer token authentication for all API requests
- **XSS prevention**: Using .textContent instead of .innerHTML for message display
- **HTTPS enforcement**: PARAM_URL validation for API URL configuration

### Performance

- **Session caching**: Reduces API calls by caching sessions with intelligent TTL
- **Lazy loading**: Drawer only rendered on course pages, not globally
- **Efficient streaming**: EventSource reuses single connection for entire message
- **CSS animations**: Hardware-accelerated transforms for smooth drawer animations
- **Minimal DOM manipulation**: Efficient token appending using textContent

### Known Issues

- **Browser compatibility**: EventSource not supported in IE11 (use polyfill if needed)
- **Session persistence**: Sessions stored in application cache, cleared on cache purge
- **Conflict with datacurso**: Cannot run both Tutor-IA implementations simultaneously (disable one)

### Migration Notes

When upgrading from local_datacurso's embedded Tutor-IA:

1. **Disable old chat**: Set `local_datacurso/enablechat` to '0'
2. **Install plugin**: Extract to `local/dttutor/` and run upgrade
3. **Migrate settings**: Run `php local/dttutor/cli/migrate_from_datacurso.php`
4. **Purge caches**: Run `php admin/cli/purge_caches.php`
5. **Test**: Verify drawer opens and chat works in a course

### Dependencies

- **Moodle**: >= 4.5 (2024042200)
- **PHP**: >= 7.4
- **External API**: Tutor-IA API (Datacurso) with SSE streaming support
- **JavaScript**: ES6 EventSource API
- **Moodle Core Modules**: jquery, core/ajax, core/notification, core/pubsub

### Upgrade Path

**From local_datacurso embedded Tutor-IA**:
- Database: No migration needed (no custom tables)
- Files: Copy avatars, update references
- Settings: Use migration script
- Templates: Already migrated
- JavaScript: Already migrated
- Hooks: New hook registration, remove old one

**Future versions**:
- Version numbers follow YYYYMMDDXX format (Moodle standard)
- Minor updates: XX increment (e.g., 2025100701)
- Major updates: Date increment (e.g., 2025100800)

---

## Version History Summary

| Version | Date       | Description                                    |
|---------|------------|------------------------------------------------|
| 1.0.0   | 2025-10-07 | Initial release - migrated from local_datacurso |

---

## Contributing

When adding entries to this changelog:

1. Follow the format: `### [Type]` where Type is Added/Changed/Deprecated/Removed/Fixed/Security
2. Use present tense ("Add feature" not "Added feature")
3. Include issue/PR numbers where applicable: `- Fixed drawer width (#123)`
4. Keep descriptions clear and concise
5. Add breaking changes to a `### Breaking Changes` section
6. Document upgrade notes under `### Migration Notes`

---

**Legend**:
- **Added**: New features
- **Changed**: Changes in existing functionality
- **Deprecated**: Soon-to-be removed features
- **Removed**: Removed features
- **Fixed**: Bug fixes
- **Security**: Vulnerability fixes
