{{ $property_name }}|
<table class=&quot;combat_unit_details_tooltip&quot;>
    <tr>
        <th>Basic value:</th>
        <td>{{ $property_value }}</td>
    </tr>
    <tr>
        <th>
            Research bonus:
            <span class=&quot;formula&quot;>(12 x 400)</span></th>
        <td>4,800</td>
    </tr>
    <tr>
        <th>
            Class Bonus:
            <span class=&quot;formula&quot;>(2 × 400)</span></th>
        <td>800</td>
    </tr>
    <tr>
        <th>
            Alliance Class Bonus:
            <span class=&quot;formula&quot;>(0 × 400)</span></th>
        <td>0</td>
    </tr>
    <tr>
        <th>
            Lifeform Tech Bonus:
            <span class=&quot;formula&quot;>(4,000 × 0%)</span></th>
        <td>0</td>
    </tr>
    <tr>
        <td colspan=&quot;2&quot; class=&quot;sum&quot;>{{ $calculated_value }}</td>
    </tr>
</table>
