# Release Notes

## v1.0.10 (2018-03-05)

### Added
- Added dynamic validation on user registration route
- photon:sync can now run on empty database table

### Changed
- Grouped artisan commands under photon namespace
- Updated artisan sync method reporting messages
- Disabled foreign key check when syncing relations
- Moved hard reset to artisan command
- Moved soft reset to artisan command
- Disabled hard and soft reset on production environment and enabled sync

### Fixed
- Fixed sync backup issues
- Fixed sync issue related to deleting module
- Fixed password reset issue
- Will not send confirmation email when USE_REGISTRATION_SERVICE_EMAIL=0
- Fixed some permission issues
- Fixed issue with generating confirmation code during user registration

### Removed
- Removed nbproject folder
- Removed some redundant files
- Removed unneded validation from login/register forms
