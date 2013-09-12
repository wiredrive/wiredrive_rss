## Using Wiredrive Media RSS Feeds

This repository
These example files are meant to be a starting place for developers to build websites using Wiredrive media RSS feeds. The feeds allow developers to build sites using content hosted in Wiredrive. The example files address the most frequent implementation questions we have received.

The XML and JSON examples show how to proxy a Wiredrive RSS feed and serve the data as raw XML or as JSON/JSONP to get around cross domain problems. Note that these examples only proxy the feed itself, **not** the media files contained within it.

If you want to use the RSS data to power an Adobe Flash video player, the Flash example demonstrates one way to do this using the JSONP proxy example and Adobe Strobe.

See the Yahoo! Developer page on [Cross-Domain requests](http://developer.yahoo.com/javascript/howto-proxy.html) for why proxying the feed through your web server is necessary.


## Code Folders:

* `/src`: Classes to parse the RSS feed, cache the file locally, and format the output for XML or json.
* `/tests`: unit tests for the Feed classes

## Example Folders:

* `/example/xml`: This file contains a example to output the unaltered RSS feed proxied and cached through the local server to get around crossdomain problems. 
* `/example/json`: This file takes the XML proxy example and changes it to support JSON/JSONP output.
* `/example/flash`: This example shows a complete demo of a web page making a JSONP request to the JSONP Proxy, building a thumbnail gallery, and playing the videos through Adobe Strobe.

## Additional Resources:

RSS section of the Wiredrive website has information on how to [get started](http://www.wiredrive.com/support/getting-started/guide-to-wiredrive-media-rss/).
