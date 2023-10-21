# Survos Grid Bundle

Use the DataTables.net javascript library with Symfony, Twig.



```bash
composer req survos/grid-bundle
```

## 

## Ideas

Import the datasets at https://domohelp.domo.com/hc/en-us/articles/360043931814-Fun-Sample-DataSets
https://www.mytechylife.com/2015/09/29/next-and-previous-row-with-jquery-datatables/
https://github.com/lerocha/chinook-database
http://2016.padjo.org/tutorials/sqlite-data-starterpacks/#more-info-simplefolks-for-simple-sql

# Dev only...

composer config repositories.survos_grid_bundle '{"type": "vcs", "url": "git@github.com:survos/SurvosGridBundle.git"}'

```bash
symfony new --demo command-demo && cd command-demo
# bump to the latest version of Symfony 6.3, use whatever version of you have installed
git clone git@github.com:tacman/symfony-demo.git
sed -i 's/"php": "8.1.0"//' composer.json
sed -i 's/"require": "6.3.*"/"require": "^6.4"/' composer.json
composer config minimum-stability dev
composer config extra.symfony.allow-contrib true
composer update
composer req survos/grid-bundle

bin/console make:controller Grid -i
```bash

symfony new grid-demo --webapp --version=next --php=8.2 && cd grid-demo
composer config minimum-stability dev
composer config extra.symfony.allow-contrib true
composer req symfony/asset-mapper:^6.4
composer req symfony/stimulus-bundle:2.x-dev

composer req survos/grid-bundle
bin/console make:controller grid -i
cat > templates/grid.html.twig <<END
{% extends 'base.html.twig' %}

{% block body %}
     <table class="table" {{ stimulus_controller('@survos/simple-datatables-bundle/table', {perPage: 5, sortable: true}) }}>
        <thead>
        <tr>
            <th>abbr</th>
            <th>name</th>
            <th>number</th>
        </thead>
        <tbody>
        {% for j in 1..12 %}
            <tr>
                <td>{{ j |date('2023-' ~ j ~ '-01') |date('M') }}</td>
                <td>{{ j |date('2023-' ~ j ~ '-01') |date('F') }}</td>
                <td>{{ j }}</td>
            </tr>
        {% endfor %}
        </tbody>
    </table>
{% endblock %}
END
symfony server:start -d
symfony open:local --path=/grid
```
