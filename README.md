#  Web App to support my RPI Picture viewer

The idea is to have my Pi contact a server to get the latest images for a QT based picture frame. The frame works like any other picture frame you can buy, but generally allows the builder to be creative with what it looks like. This means you need to build it all, which can be some what challenging.

The Pi will contact the web server and ask for an XML file that details all the images available. It will download any new images, and delete any that are no longer in the list. The list can be arbitrarily long, but I will artifically cap it to be about 30 pictures. More than that, and it will take too long to cycle through them all.

