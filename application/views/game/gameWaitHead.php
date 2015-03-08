<?php
/**
 *
 * Copyright 2011-2015 David Rodal
 *
 *  This program is free software; you can redistribute it
 *  and/or modify it under the terms of the GNU General Public License
 *  as published by the Free Software Foundation;
 *  either version 2 of the License, or (at your option) any later version
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
/**
 * User: drodal
 * Date: 12/6/11
 * Time: 8:18 PM
 */
?>
<head>
  <style type="text/css">
      @font-face {
          font-family: Military;
          src: url('<?=base_url("Maparmy.ttf");?>');
      }

      .mil {
          font-family: Military;
          font-size: 22px;
      }
  </style>
  <link href="<?php echo base_url();?>js/jquery-ui.css" rel="stylesheet" type="text/css"/>
  <script src="<?php echo base_url();?>js/jquery.min.js"></script>
  <script src="<?php echo base_url();?>js/jquery-ui.min.js"></script>
  <style type="text/css">
      #yourside {
          width: 33%;
          float: left;
      }

      #themap {
          width: 33%;
          border: 1px solid blue;
          height: 400px;
          float: left;
          position: relative;
      }

      #themap div {
          position: absolute;
          width: 50%;
          height: 100%;
          top: 0px;
      }

      #themap #battle0 {
          left: 0px;
          text-align: right;
      }

      #themap #battle1 {
          right: 0px;
          text-align: left;
      }

      #draggable {
          border: 0px solid red;
          z-index: 100;
          position: absolute;
          width: 50px;
          height: 50px;
          background: silver;
      }

      #city {
          background-color: red;
          border: 0px solid red;
          position: absolute;
          top: 300px;
          width: 100px;
          height: 100px;
      }

      #city div {
          background: #ccf;
          height: 100px;
      }

      #army li, #enemy li, #building .bui {
          float: left;
      }

      #building .bui, #battle .bui {
          position: relative;
          width: 30px;
          text-align: center;
      }

      #clock {
          font-size: 120px;
      }
  </style>
  <script>function Sync(arg) {
      this.id = "Sync";
      this.callbacks = Object;
      this.lengths = {};
      this.register = function(name, callback) {
          this.callbacks[name] = callback;
          this.lengths[name] = 0;
      }
      this.fetch = function(last_seq, args) {
          var chatsIndex = 0;
          var theArgs = {}
          if (args) {
              chatsIndex = parseInt(args.chatsIndex);
              theArgs = args;
          }
          that = this;
          $.ajax(
                  {url: "<?=site_url("game/fetch/$lobby") . "/";?>" + last_seq,
                      type:"POST",
                      data:theArgs,
                      success:function(data, textstatus) {
                          fetchArgs = {};
                          for (var i in that.callbacks) {
                              if (data[i]) {
                                  if ($.isArray(data[i])) {
                                      var lastlength = that.lengths[i];
                                      //that.lengths[i] = data[i].length;
                                      data[i].splice(0, lastlength);
                                  }
                                  if (true) {
                                      /*var prev = that.cache[i];
                                that.cache[i] = data[i].length;
                                if(prev)
                                    data[i] = data[i].splice(0,prev);*/
                                  }
                                  that.callbacks[i](data[i]);
                                  if (data[i + "Index"]) {
                                      fetchArgs[i + "Index"] = data[i + "Index"];
                                  }
                              }
                          }
                          last_seq = data.last_seq;
                          that.fetch(last_seq, fetchArgs);
                      },
                      complete:function(jq, textstatus) {
                          if (textstatus != "success")that.fetch(0);
                      }
                  });
      }
  }
  x = new Sync("myArg");
  x.register("games", function(games) {

     alert("dude"+games);
  });

  x.fetch(0);

  function doit(type) {
      if (!type) {
          var mychat = $("#mychat").attr("value");
          $.ajax({url: "<?=site_url("game/add/$lobby");?>",
              type: "POST",
              data:{chats:mychat},
              success:function(data, textstatus) {
              }
          });
      } else {
          $.ajax({url: "<?=site_url("game/add/$lobby");?>",
              type: "POST",
              data:type,
              success:function(data, textstatus) {
              }
          });
      }
      $("#mychat").attr("value", "");
  }

  </script>
  </head>
