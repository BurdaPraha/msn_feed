# MSN FEED for Burda Praha


## Installation
- Download via `composer require burdapraha/msn_feed dev-master`
- Install module `drush en -y msn_feed`
- configure view - MSN Feed field formatter becomes available
- some fields/paragraph require special attention:
  - set paragraph display in view to feed and enable feed view mode for:
    - youtube video
    - image
    - video
  - all the other paragraphs will be default to default
  Teaser image field needs to display target id

