RefreshCache Extra for MODx Revolution
======================================

**Author:** Bob Ray [Bob's Guides](https://bobsguides.com)

Documentation is available at [Bob's Guides](https://bobsguides.com/refreshcache-tutorial.html)


The RefreshCache snippet refreshes the cache by calling MODX Manager code to generate cache files. This is much faster than the previous version. 

RefreshCache no longer visits each page with cURL, so it is less likely to affect hit counters, trigger events, or execute snippet code on a page.

RefreshCache now creates the cache files for regular resources, Articles resources, and pages hidden by ACL entries. It skips SymLinks, WebLinks, and the ArticlesContainer. It also skips unpublished, deleted, non-cacheable, and (optionally), files hidden from menus.

The point is for this program to spend time waiting for the pages so
the site visitors don't have to. They'll see cached pages, which will be
delivered much faster.
 
 
The larger the site, the longer it takes, but the new version is much faster. The CRON version (and the snippet version) can update about 25 resources per second. The CMP version, with the progress bar, will update about 4 resources per second.

Important: Make sure the RefreshCache resource is unpublished, or not marked as cacheable. Do the same for the CacheClear snippet if you have it, or it will undo RefreshCache's work.
