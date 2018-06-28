# CHANGELOG for version 2.0

## Symfony 3 and 4 support

As of this version, Symfony in versions 2, 3 and 4 is suported.

## Introduced new events

Now, before and after changing the position of an element an event of class 
`FSi\Bundle\AdminPositionableBundle\Event\PositionableEvent` is fired. Refer to
the table below for more specific information.

<table>
    <thead>
        <th>Event class property</th>
        <th>Event name</th>
    </thead>
    <tbody>
        <tr>
            <td>FSi\Bundle\AdminPositionableBundle\Event\PositionableEvents::PRE_APPLY</td>
            <td>admin.positionable.pre_apply</td>
        </tr>
        <tr>
            <td>FSi\Bundle\AdminPositionableBundle\Event\PositionableEvents::POST_APPLY</td>
            <td>admin.positionable.post_apply</td>
        </tr>
    </tbody>
</table>

## Dropped support for PHP below 7.1

To be able to fully utilize new functionality introduced in 7.1, we have decided 
to only support PHP versions equal or higher to it.
