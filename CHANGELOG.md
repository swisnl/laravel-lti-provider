# Changelog

All notable changes to `laravel-lti-provider` will be documented in this file.

Updates should follow the [Keep a CHANGELOG](https://keepachangelog.com/) principles.

## NEXT - YYYY-MM-DD

### Added
- Nothing

### Deprecated
- Nothing

### Fixed
- Saving a loaded user result created a duplicate `lti_user_results` record instead of updating the existing one, e.g. when a platform issues a new `lis_result_sourcedid` on every launch. `saveUserResult()` now decides between insert and update based on the record id (consistent with the other save methods) and loaded user results now expose their `created`/`updated` timestamps.

### Removed
- Nothing

### Security
- Nothing

## 0.2.0 - 2025-11-05

### Added
- Laravel 12 support.

## 0.1.1 - 2024-07-02

### Added
- Laravel 11 support.

## 0.1.0 - 2021-11-24

Initial implementation of the package.
