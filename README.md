RA/Dec to Alt/Az PHP Class
================

This class converts celestial coordinates, along with the time, latitude and longditude to a altitude and azimuth for a telescope mount or camera.

##Installation

```
include('radec.class.php');
```

##Usage

```
$radec = new radec(radec::decimaldegrees(latitude), radec::decimaldegrees(longditude));

$radec->setradec(radec::decimaldegrees(right_ascention), radec::decimaldegrees(declination));

$time = strtotime('now');

$azimuth = $radec->getAZ($time);
$altitude = $radec->getALT($time);
```
