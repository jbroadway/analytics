This is a simple Google Analytics app written for the [Elefant CMS](http://github.com/jbroadway/elefant).
It embeds the Analytics code into pages, and allows the site admin
to update the site ID through the Elefant admin toolbar.

To install, unzip it into your apps folder. You'll see "Analytics" appear
in the Elefant Tools menu. Click on it to set your site ID.

Next, add the following code to your layouts where you want the tracking
code to appear (usually just before `</body>`):

```
{! analytics/code !}
```

For extra speed you can tell Elefant to hard-code the Analytics code into
the template so the app is only called when the template is first compiled. 
To do this, change the above embed code into the following:

```
{# analytics/code #}
```

The one thing to be aware of is that if your site ID is updated in the future,
the old code will be hard-coded in the compiled templates, so you'll need
to recompile the layout templates. The [Assetic app](http://github.com/jbroadway/assetic)
has a handy button to do this in a single click.
