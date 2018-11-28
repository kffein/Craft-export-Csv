# Export CSV plugin for Craft CMS 3.x

![Screenshot](resources/img/header.png)

## Requirements

This plugin requires Craft CMS 3.0.0-beta.23 or later.

## Installation

To install Export CSV, follow these steps:

1. Install with Composer via composer require kffein/craft-export-csv from your project directory
2. Install plugin in the Craft Control Panel under Settings > Plugins

You can also install Export CSV via the Plugin Store in the Craft AdminCP.

## Overview

This plugin will allow multiple csv export configurations 

## Configuration

![Screenshot](resources/img/settings.png)

When saving an export, you can select a section and enter the filename you want to export locally. Then you can select fields to be exported for the csv. There are currently two types of rows that you can create ( one field or multiple field and custom string) :

- **Handle** : Written without brackets and can access created fields and craft entry attributes

- **Concat handle** : Each fields are written within brackets.


You can also configure the number of rows to be execute for each job. This prevent server to timeout during the export.

## Using craft-export-csv

![Screenshot](resources/img/reports.png)

On Export CSV plugin page, you can manage exports. Then you can download the last version of the file generated. Generating a new file will overwrite the previous csv file.

## Roadmap

Some ideas for potential features:

* Modifiy existing exports settings
* UI more dynamic

## Credits

- Icon by Dev Patel from the Noun Project

Brought to you by [KFFEIN](http://kffein.com)
