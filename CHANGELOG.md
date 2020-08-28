# craft-export-csv Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/) and this project adheres to [Semantic Versioning](http://semver.org/).

## 1.0.9 - 2020-08-28
### Added
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
