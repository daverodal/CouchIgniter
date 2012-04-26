// form.js

// copyright (c) 2009-2011 Mark Butler
// This program is free software; you can redistribute it 
// and/or modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation;
// either version 2 of the License, or (at your option) any later version. 

// main classes for wargame
var mapData;
var force;
var terrain;
var moveRules;
var combatRules;
var gameRules;
var prompt

// map image data
var mapOffsetX;
var mapOffsetY;

// mapGrid values
// global constants
var OriginX;
var OriginY;
var TopHexagonHeight;
var BottomHexagonHeight;
var HexagonEdgeWidth;
var HexagonCenterWidth;
var MaxRightHexagon;
var MaxBottomHexagon;
// counter image values
var oneHalfImageWidth;
var oneHalfImageHeight;

var maxTurn;

// textboxes on form
var gameStatusText;
var hexagonText;
var terrainText;
var counterText;
var promptText;

// ------- initialize classes ------------------------------
//mapData = new MapData();
//force = new Force();
//terrain = new Terrain();
//moveRules = new MoveRules(force, terrain);
//combatRules = new CombatRules(force, terrain);
//gameRules = new GameRules(moveRules, combatRules, force);
//prompt = new Prompt(gameRules, moveRules, combatRules, force, terrain);
// ------- end initialize classes --------------------------

//function mapMouseMove(event) {
//
//    var mapGrid;
//    mapGrid = new MapGrid(mapData);
//    var hexagon;
//
//    var pixelX, pixelY;
//    // get pixel coordinates
//    // this for Netscape browsers
//    if ( document.addEventListener ) {
//        pixelX = event.pageX - event.target.offsetLeft;
//        pixelY = event.pageY - event.target.offsetTop;
//    }
//    // this for IE browsers
//    else {
//        pixelX =  event.offsetX;
//        pixelY =  event.offsetY;
//    }
//
//    // update form text
//    mapGrid.setPixels( pixelX, pixelY );
//
//    hexagonText.innerHTML = "&nbsp;";
//    terrainText.innerHTML = "&nbsp;";
//
//    if( terrain.terrainIs(mapGrid.hexagon, "offmap") == false)
//    {
//        hexagonText.innerHTML = mapGrid.hexagon.getName();
//        hexagonText.innerHTML += "&nbsp;";
//
//        terrainText.innerHTML = terrain.getTerrainDisplayName(mapGrid.getHexpart());
//        terrainText.innerHTML += terrain.getTownName(mapGrid.getHexagon());
//        terrainText.innerHTML +=  "&nbsp;"
//    }
//    promptText.innerHTML = prompt.getPrompt(OVER_MAP_EVENT, MAP, mapGrid.getHexagon());
//    promptText.innerHTML += "&nbsp;";
//    counterText.innerHTML = "&nbsp;<br />&nbsp;<br />&nbsp;<br />&nbsp;<br />&nbsp;";
//}

function mapMouseDown(event) {


    var pixelX, pixelY;
    // get pixel coordinates
    // this for Netscape browsers
    if ( document.addEventListener ) {
        pixelX = event.pageX - event.target.offsetLeft;
        pixelY = event.pageY - event.target.offsetTop;
    }
    // this for IE browsers
    else {
        pixelX =  event.offsetX;
        pixelY =  event.offsetY;
    }
    var p;
    p = $("#map").offset();
    pixelX -= p.left;
    pixelY -= p.top;
//    alert("PixelX "+ pixelX+ " PixelY "+pixelY);

     doitMap(pixelX,pixelY);

}

//function mapMouseOut(event) {
//
//    // this for all browsers
//    hexagonText.innerHTML = "&nbsp;";
//    terrainText.innerHTML = "&nbsp;";
//    counterText.innerHTML = "&nbsp;<br />&nbsp;<br />&nbsp;<br />&nbsp;<br />&nbsp;";
//    promptText.innerHTML = "&nbsp;";
//    window.defaultStatus = "";
//}

//function counterMouseMove(event) {
//
//    var id;
//
//    if (document.addEventListener) {
//        id = event.target.id.toString();
//    }
//    // this for IE browsers
//    else {
//        id = event.srcElement.id.toString();
//    }
//
//    // update form text
//    var hexagon = force.getUnitHexagon(id);
//    var hexpart = new Hexpart(hexagon.getX(), hexagon.getY());
//
//    hexagonText.innerHTML = "&nbsp;";
//    terrainText.innerHTML = "&nbsp;";
//
//    if ( terrain.isOnMap(hexagon) == true && force.units[id].status != STATUS_CAN_REINFORCE ) {
//        hexagonText.innerHTML = hexagon.getName();
//        hexagonText.innerHTML += "&nbsp;";
//
//        terrainText.innerHTML = terrain.getTerrainDisplayName(hexpart);
//
//        terrainText.innerHTML += terrain.getTownName(hexagon);
//        terrainText.innerHTML +=  "&nbsp;"
//    }
//
//    counterText.innerHTML = force.getUnitInfo( id );
//    promptText.innerHTML = prompt.getPrompt(OVER_COUNTER_EVENT, id, hexagon);
//    promptText.innerHTML += "&nbsp;";
//}

function counterMouseDown(event) {
    var id;
    if ( document.addEventListener ) {
        id = event.target.id.toString();
    }
    // this for IE browsers
    else {
        id = event.srcElement.id.toString();
    }
    doitUnit(id);
}

function nextPhaseMouseDown(event) {
    doitNext();

}

function attachMouseEventsToMap(objectName) {

    object = document.getElementById(objectName);
    // this for Netscape browsers
    if ( object.addEventListener ) {
//        object.addEventListener("mousemove", mapMouseMove, true);
        object.addEventListener("mousedown", mapMouseDown, true);
//        object.addEventListener("mouseout", mapMouseOut, true);
    }
    // this for IE browsers
    else {
//        object.attachEvent("onmousemove", mapMouseMove);
        object.attachEvent("onmousedown", mapMouseDown);
//        object.attachEvent("onmouseout", mapMouseOut);
    }

    mapOffsetX = parseInt(object.style.left);
    mapOffsetY = parseInt(object.style.top);

    return true;
}

function attachMouseEventsToCounter(objectName) {

    var id;
    id = parseInt(objectName, 10);

    object  = document.getElementById(objectName);

    // this for Netscape browsers
    if ( object.addEventListener ) {
//        object.addEventListener("mousemove", counterMouseMove, true);
        object.addEventListener("mousedown", counterMouseDown, true);
//        object.addEventListener("mouseout", mapMouseOut, true);
        return true;
    }
    // this for IE browsers
    else {
//        object.attachEvent("onmousemove", counterMouseMove);
        object.attachEvent("onmousedown", counterMouseDown);
//        object.attachEvent("onmouseout", mapMouseOut);
        return true;
    }
}

function attachMouseEventsToButton(objName) {

    obj = document.getElementById(objName);
    if ( obj.addEventListener != null ) {
        obj.addEventListener("mousedown", this.nextPhaseMouseDown, true);
        return true;
    }
    else {
        obj.attachEvent("onmousedown", this.nextPhaseMouseDown);
        return true;
    }
}

function moveCounter(id) {

    var mapGrid;
    mapGrid = new MapGrid(mapData);

    var counterObj;
    counterObj = document.getElementById( id );

    mapGrid.setHexagonXY( this.force.getUnitHexagon(id).getX(), this.force.getUnitHexagon(id).getY());

    var x = mapGrid.getPixelX() - (document.getElementById("map").width) - (parseInt(id) * document.getElementById(id).width) - (document.getElementById(id).width / 2);
    //x = - document.getElementById("map").width;
    //if (id == 0) alert(x);
    //x = 0;
    counterObj.style.left = x + "px";
    var y = mapGrid.getPixelY() - (document.getElementById(id).height / 2);
    //y = 0;
    counterObj.style.top = y + "px";
}

function updateForm() {
    var id;

    for ( id = 0; id < force.units.length; id++ ) {
        this.moveCounter( id );
    }
    gameStatusText.innerHTML = gameRules.getInfo();
}

function createImage(id, src, x, y)
{
    var newImage = document.createElement("img");
    newImage.setAttribute("id", id);
    newImage.setAttribute("alt", id);
    newImage.setAttribute("src", src);
    newImage.setAttribute("class", "counter");
    newImage.style.position = "relative";
    newImage.style.left = x + "px";
    newImage.style.top = y + "px";

    document.getElementById("gameImages").appendChild(newImage);
}

function initialize() {

    // setup events --------------------------------------------
    this.attachMouseEventsToMap("map");

    var id;
        for(id = 0;id < 6;id++){
            this.attachMouseEventsToCounter(id);
        }
//    for ( id = 0; id < force.units.length; id++ ) {
//        createImage( id, force.units[id].image, 0, 0 );
//        this.attachMouseEventsToCounter( id );
//    }

    attachMouseEventsToButton("nextPhaseButton");
    // end setup events ----------------------------------------

    // for web browsers that have addEventListener
    //if ( document.addEventListener ) {
    gameStatusText = document.getElementById("gameStatusText");
    hexagonText = document.getElementById("hexagonText");
    terrainText = document.getElementById("terrainText");
    counterText = document.getElementById("counterText");
    promptText = document.getElementById("promptText");
    //}

    counterText.innerHTML = "counter&nbsp;<br />&nbsp;<br />&nbsp;<br />&nbsp;<br />&nbsp;";

    //mapText.innerHTML = this.terrain.getTerrainList();

    updateForm();
}
$(function(){initialize();});