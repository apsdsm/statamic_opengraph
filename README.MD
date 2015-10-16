# OpenGraph

This plugin provides a fieldset for quickly adding opengraph data to your content types, and some helper functions for adding social data to your views. I've used this in a few Statamic sites to speed up the process of getting share meta onto the page.

I'm not sure how much I'll be using Statamic from here on out, so I can't guarantee how often I'll touch this code, but at the very least it might give you a good idea how this stuff could be done with a plugin... At least until they release Statamic 2, which may make everything here redundant. :heart:

## Dependencies

None, but the social buttons reference the Font Awesome icon set.

## Installation

- unzip to Statamic directory
- adjust the settings file in `_config/add-ons/opengraph`
- if you want to add customisable opengraph data to pages, add the `opengraph` fieldset to the respective filedset file

## Usage

### Analytics Code

This will add the Javascript for Google Analytics to your layout. The analytics code is stored in the plugin config file.

```
{{ opengraph:analytics }}
```

### Opengraph Meta

This will add a bunch of open graph metadata that will make your layouts more share friendly, and possibly increase your SEO.

```
{{ opengraph:metatags }}
```

### Share Buttons

This will make a small list of share buttons in your layout. They use the same data as the meta tags, and they're very plain - just enough to get you going with social without having to import a bunch of extra Javascript to get things like page counts, etc. Think of this as a very basic starter method that will get you through unless you want to be a big-shot Social Giant.

```
{{ opengraph:sharebuttons }}
```



