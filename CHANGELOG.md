# craft-export-csv Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/) and this project adheres to [Semantic Versioning](http://semver.org/).

## 4.1.1 - 2022-12-14
### Fix
- Ensure composer.json version matches tag

## 4.1.0 - 2022-12-14
### Added
- Allow editing of a previously-created export
- Allow an export to be duplicated
- Include a name for the export and reformat reports screen
- Allow empty field values
- Allow exported entries to be expired
### Fix
- Assignment operator should be equality operator

## 4.0.0 - 2022-12-11
### Added
- Convert to craft-4 using craftcms/rector
- Add .editorconfig
- Bump to 4.0.0
### Fix
- Fix typo
- Ensure editableTableField is visible in craft-4
-  Standardise formatting

## 1.1.1 - 2020-11-16
### Added
- Add support for Craft 3.6

## 1.0.13 - 2021-03-10
### Fix
- Fix compatibility issues with Craft CMS 3.4.x

## 1.0.12 - 2020-11-16
### Fix
- Change composer version

## 1.0.11 - 2020-11-16
### Fix
- Fix namespace for composer v2 compliance

## 1.0.10 - 2020-08-28
### Fix
- Update package configuration

## 1.0.9 - 2020-08-28
### Fix
- Fix controllers errors
- Update UI for Craft 3.5

## 1.0.8 - 2019-04-24
### Added
- Add support for entries status

## 1.0.7 - 2019-04-24
### Added
- Add support for assets. Return the url of the first one.
- Add support for multisites.

## 1.0.6 - 2018-12-10
### Fix
- Fix array/object export fields data. Convert to string with json_encode()

## 1.0.5 - 2018-11-28
### Fix
- Update README.md

## 1.0.4 - 2018-11-05
### Fix
- Fix minor error if the number of rows was empty.
- Fix translation error.

## 1.0.3 - 2018-10-29
### Fix
- Changed temporary file location. Recomanded to regenerate all existing exports.
- Generating existing reports now update it by removing the old one.
- Fixed error with custom handle.

## 1.0.2 - 2018-10-10
### Fix
- Fixed filename variable conversion
- Fixed download location minor bugs
- Fixed reports sections error when exports list is empty

## 1.0.1 - 2018-10-10
### Added
- Initial release
