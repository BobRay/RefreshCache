RefreshCache Extra for MODx Revolution
======================================

**Author:** Bob Ray [Bob's Guides](http://bobsguides.com)

Documentation is available at [Bob's Guides](http://bobsguides.com/refreshcache-tutorial.html)


The RefreshCache snippet refreshes the cache by visiting all site pages that are
published, undeleted, cacheable, and not hidden from menus.
 
The point is for this program to spend time waiting for the pages so
the site visitors don't have to. They'll see cached pages, which will be
delivered much faster.
 
RefreshCache is an inelegant, brute-force snippet. It refreshes the cache by
requesting every page with cURL.
 
The larger the site, the longer it takes. It's intentionally slow to avoid
stressing the server and to keep from running afoul of bot-blocking software.
 
On a 100-page site, at broadband speeds, it can take 5-10 minutes to run,
depending on the connection speed and how complex the pages are.
Larger sites can take much longer.
 
The only settable property is &delay, which sets the number of seconds to sleep
between page requests. The default is .51 seconds. It should not be set lower than this.
 
To install, paste the code into a snippet called RefreshCache. Create a resource
called RefreshCache with just the snippet tag: [[!RefreshCache]].
 
The resource should not be cacheable and the alias should be
refresh-cache.
 
Create an empty template with just this tag: [[*content]] and be sure to
assign that template to the resource.
 
To run the snippet, preview the ClearCache resource.

Note that if you close the browser window during the run, the process will abort. The next time you run RefreshCache the display will be corrupted and you'll see multiple progress bars that will go beyond 100%. To fix this, just run RefreshCache again and let it finish.
