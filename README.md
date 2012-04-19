This is a site search app written for the [Elefant CMS](http://github.com/jbroadway/elefant) powered by [ElasticSearch](http://www.elasticsearch.org/) or [IndexTank](https://github.com/linkedin/indextank-engine).

To install:

1\. Unzip it into your apps folder

2\. Edit the apps/search/conf/config.php and add your ElasticSearch servers or IndexTank credentials

3\. Add the following hooks to your global `conf/config.php`:

```
admin/add[] = search/add
admin/edit[] = search/add
admin/delete[] = search/delete
blog/add[] = search/add
blog/edit[] = search/add
blog/delete[] = search/delete
```

Other apps may also have additional hooks that enable them to be added to the search, such as:

```
events/add[] = search/add
events/edit[] = search/add
events/delete[] = search/delete

wiki/edit[] = search/add
```

4\. Either add a search box to your template with this tag:

```
{! search/index !}
```

Or add it to a page on your site by clicking the Dynamic Objects button in the
wysiwyg editor and choosing "Search: Search Box".

## IndexTank hosts

Here are some companies that provide hosted IndexTank services to make it easier to get started:

* [Searchify](http://www.searchify.com/)
* [HoundSleuth](http://www.houndsleuth.com/)
* [IndexTank To Go](http://launch.indextanktogo.com/)
