# FSiAdminPositionableBundle

This bundle provides way to change position of item on the list gradually.
This mean that position will be changed by 1.

`FSiAdminPositionableBundle` works with conjunction with [Sortable behavior extension for Doctrine2](https://github.com/l3pp4rd/DoctrineExtensions/blob/master/doc/sortable.md)

## Usage

Add to `AppKernel.php`:

```php
new FSi\Bundle\AdminPositionableBundle\FSiAdminPositionableBundle(),
```

Add routes to `/app/config/routing.yml`:

```yml
_fsi_positionable:
    resource: "@FSiAdminPositionableBundle/Resources/config/routing.xml"
    prefix:   /
```

Sample entity:

```php
use use FSi\Bundle\AdminPositionableBundle\Model\PositionableInterface;

class Promotion implements PositionableInterface
{
    /**
     * {@inheritdoc}
     */
    public function increasePosition()
    {
        $this->position++;
    }

    /**
     * {@inheritdoc}
     */
    public function decreasePosition()
    {
        $this->position--;
    }
}
```

**Note:** that `PositionableInterface` implementation is required

Sample datagrid definition:

```yml
columns:
  title:
    type: text
    options:
      display_order: 1
      label: "backend.promotions.datagrid.title.label"
  actions:
    type: action
    options:
      display_order: 2
      label: "backend.promotions.datagrid.actions.label"
      field_mapping: [ id ]
      actions:
        move_up:
          route_name: "fsi_admin_positionable_decrease_position"
          additional_parameters: { element: "promotions" }
          parameters_field_mapping: { id: id }
        move_down:
          route_name: "fsi_admin_positionable_increase_position"
          additional_parameters: { element: "promotions" }
          parameters_field_mapping: { id: id }
```

Sample datagrid buttons:

`/src/FSi/Bundle/WebBundle/Resources/views/Admin/datagrid.html.twig`:

```twig
{% extends '@FSiAdmin/CRUD/datagrid.html.twig' %}

{% block datagrid_rowset %}
    {% spaceless %}
        {% set rowsCount = datagrid|length %}
        {% for index, row in datagrid %}
            {% set rowIndex = loop.index %}
            <tr>
                {% for cell in row %}
                    {{ datagrid_column_cell_widget(cell, { rowIndex: rowIndex, rowsCount: rowsCount }) }}
                {% endfor %}
            </tr>
        {% endfor %}
    {% endspaceless %}
{% endblock %}

{% block datagrid_column_name_actions_cell %}
    {% spaceless %}
        <td class="cell">
            <div class="text-right">
                {% for action_name, action in cell.value %}
                    {% if action_name == 'move_up' %}
                        {% if vars.rowIndex == 1 %}
                            <button class="btn btn-warning btn-sm disabled">
                                <span class="glyphicon glyphicon-arrow-up icon-white"></span>
                            </button>
                        {% else %}
                            <a href="{{ action.url }}" class="btn btn-warning btn-sm" title="{{ "backend.datagrid.actions.move_up.label"|trans }}">
                                <span class="glyphicon glyphicon-arrow-up icon-white"></span>
                            </a>
                        {% endif %}
                    {% elseif action_name == 'move_down' %}
                        {% if vars.rowIndex == vars.rowsCount %}
                            <button class="btn btn-warning btn-sm disabled">
                                <span class="glyphicon glyphicon-arrow-down icon-white"></span>
                            </button>
                        {% else %}
                            <a href="{{ action.url }}" class="btn btn-warning btn-sm" title="{{ "backend.datagrid.actions.move_down.label"|trans }}">
                                <span class="glyphicon glyphicon-arrow-down icon-white"></span>
                            </a>
                        {% endif %}
                    {% else %}
                        {{ datagrid_column_type_action_cell_action_widget(
                            cell, action_name, action.content, action.url_attr|merge({'href' : action.url})
                        ) }}
                    {% endif %}
                {% endfor %}
            </div>
        </td>
    {% endspaceless %}
{% endblock %}
```
