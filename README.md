## Example files for use with Wiredrive Media RSS Feeds

These example files are meant to be a starting place for developers to build websites using Wiredrive media RSS feeds. The feeds allow developers to build sites using content hosted in Wiredrive. The example files address the most frequent implementation questions we have received.

### Flash users:
If you want to import the RSS data into Adobe Flash, look at the json and xml examples.  These examples are for proxing the raw data from the RSS feed to your local server to get around crossdomain problems. Note, however, that these examples only proxy the *RSS feed itself*, **not** the media files themselves.

See the Yahoo! Developer page on [Cross-Domain requests](http://developer.yahoo.com/javascript/howto-proxy.html) for why proxying the feed through your web server is necessary.


## Code Folders:

* `/src`: Classes to parse the RSS feed, cache the file locally, and format the output for XML or json.
* `/tests`: unit tests for the Feed classes

## Example Folders:

* `/example/flowplayer`: Example for using the flowplayer with the RSS feed
* `/example/json`: This file contains a example to output the RSS feed as raw json data. 
* `/example/jwplayer`: Example for using the JW Player with the RSS feed.
* `/example/php`: This file contains a example layout done entirely in PHP and CSS. 
* `/example/xml`: This file contains a example to output the unaltered RSS feed proxied and cached through the local server to get around crossdomain problems. 

## Additional Resources:

RSS section of the Wiredrive website has information on how to [get started](http://www.wiredrive.com/support/getting-started/guide-to-wiredrive-media-rss/).
