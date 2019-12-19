# GeoCalc
A PHP class made to work with distances between cities.

# Basic usage

`$unit = GeoCalc.KILOMETRES;`<br/>
`$valencia = new GeoCalc("Valencia", null, $unit);`<br/>
`$madrid = new GeoCalc("Madrid", null, $unit);`<br/>
`echo "Is Valencia near Madrid?";`<br/>
`echo ($valencia->isNear($madrid), 100) ? `<br/>
`"Yes it is!" : "Nope, there are ".$valencia->getDistanceFrom($madrid)." kilometres.";`
