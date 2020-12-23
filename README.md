# Section + Product Type plugin for Craft CMS 3.x

## Requirements

This plugin requires Craft CMS 3.0.0-beta.23 or later.

## Installation

To install the plugin, follow these instructions.

1. Open your terminal and go to your Craft project:

        cd /path/to/project

2. Then tell Composer to load the plugin:

        composer require wmd/section-and-product-type

3. In the Control Panel, go to Settings → Plugins and click the “Install” button for Section And Product Type.

## Section And Product Type Overview

The plugin is a field for selecting sections and types of commerce products.

## Configuring Section And Product Type

When you create fields with one of these types, you can choose which sections or product types will be available for selection.
It is possible to enable multi-selection, which makes it possible to select several elements.
## Using Section And Product Type

Steps:
1. Installing the plugin;
2. Creation and configuration of fields;
3. Add a field to the Entry Type of Section;


Usage in the template

    {# section field #} 
    
    {% set sectionsIds = enty.mySectionField %}
    {% set sections = craft.entries.sectionId(sections).all() %}

    {% for section in sections %}
       {# code for output #}
    {% endfor %}   
    
    {# product type field #} 
    
    {% set productTypeIds = enty.myProductTypeField %}
    {% set products = craft.products().typeId(productTypeIds).all() %}
    
    {% for product in products %}
           {# code for output #}
    {% endfor %}

The plugin is based on the logic of the plugin https://github.com/charliedevelopment/craft3-section-field, many thanks to the author.

Brought to you by [WMD Hosting](https://wmd.hosting/)
