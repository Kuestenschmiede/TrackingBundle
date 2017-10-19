// "namespace"
this.c4g = this.c4g || {};
this.c4g.maps = this.c4g.maps || {};
this.c4g.maps.hook = this.c4g.maps.hook || {};
this.c4g.maps.hook.layerswitcher_forEachItem = this.c4g.maps.hook.layerswitcher_forEachItem || [];

(function ($, c4g) {
  'use strict';

c4g.maps.hook.layerswitcher_forEachItem.push(
  function (objParam) {

    var filterBtn,
        uid;

    new c4g.maps.hook.Trackingdatafilter(objParam.that);

    if (c4g.maps.layers[objParam.entry.data('uid')].filterable) {

      /*uid = objParam.entry.data('uid');

      filterBtn = document.createElement('a');
      filterBtn.setAttribute('href', '#');
      filterBtn.appendChild(document.createTextNode(c4g.maps.constant.i18n.TRACKING_FILTER_TITLE));

      objParam.entry.parent().append(filterBtn);

      filterBtn = $(filterBtn);
      filterBtn.addClass(c4g.maps.constant.css.STARBOARD_ITEM_FILTER_BUTTON);
      objParam.entry.addClass(c4g.maps.constant.css.STARBOARD_ITEM_HAS_FILTER);

      filterBtn.data('uid', 'filter_' + uid);
      filterBtn.data('filterParams', c4g.maps.layers[objParam.entry.data('uid')].filterable);
      filterBtn.addClass(c4g.maps.constant.css.INACTIVE);
      filterBtn.click(function(event) {

        event.preventDefault();

        if (filterBtn.hasClass(c4g.maps.constant.css.INACTIVE))
        {
          filterBtn.removeClass(c4g.maps.constant.css.INACTIVE);
          filterBtn.addClass(c4g.maps.constant.css.ACTIVE);
        } else {
          filterBtn.removeClass(c4g.maps.constant.css.ACTIVE);
          filterBtn.addClass(c4g.maps.constant.css.INACTIVE);
        }

        objParam.that.proxy.plugins.trackingdatafilter.plugin.changeFromLayer('filter_' + uid, c4g.maps.layers[objParam.entry.data('uid')].filterable);
      });*/



    }

  }
);

  c4g.maps.hook.Trackingdatafilter = function(Layerswitcher) {

    this.layerswitcher = Layerswitcher;

    this.layerswitcher.proxy.plugins = this.layerswitcher.proxy.plugins || {};
    this.layerswitcher.proxy.plugins.trackingdatafilter = this.layerswitcher.proxy.plugins.trackingdatafilter || {};

    if (!this.layerswitcher.proxy.plugins.trackingdatafilter.isLoaded) {
      this.create();
      this.layerswitcher.proxy.plugins.trackingdatafilter.isLoaded = true;
      this.layerswitcher.proxy.plugins.trackingdatafilter.plugin = this;
    }

  }

  // Add methods
  c4g.maps.hook.Trackingdatafilter.prototype = $.extend(c4g.maps.hook.Trackingdatafilter.prototype, {

    /**
     * @TODO: [create description]
     *
     * @return  {[type]}  [description]
     */
    create: function () {

      /*var self,
          contentWrapper,
          contentHeadline,
          contentInfo;

      self = this;
      self.activeLayerFilter = {};
      self.filterLayerReference = {};

      // create filter wrapper
      contentWrapper = document.createElement('div');
      contentWrapper.className = c4g.maps.constant.css.STARBOARD_FILTER_WRAPPER;

      // create filter headline
      contentHeadline = document.createElement('h4');
      contentHeadline.innerHTML =  c4g.maps.constant.i18n.TRACKING_FILTER_TITLE;
      contentWrapper.appendChild(contentHeadline);

      // create wrapper for inputs
      this.contentDiv = document.createElement('div');
      this.contentDiv.className = c4g.maps.constant.css.STARBOARD_FILTER_INPUT_WRAPPER;
      //contentInfo = document.createElement('p');
      //this.contentDiv.appendChild(contentInfo);

      this.$contentDiv = $(this.contentDiv);

      self.dateTimePickerOptions = {
        'dateFormat': "DD.MM.YYYY hh:mm",
        'locale': "de",
        'firstDayOfWeek': 1,
        'closeOnSelected': true,
        'autodateOnStart': false
      };

      // from input
      this.$inputFrom = $('<input type="text" name="from" class="' + c4g.maps.constant.css.PLUGIN_DATETIMEPICKER_CLASS + '" placeholder="' + c4g.maps.constant.i18n.TRACKING_FILTER_FROM_PLACEHOLDER + '">');
      this.$inputFrom.focus(function(){
        $(this).removeClass(c4g.maps.constant.css.ERROR);
      });

      // to input
      this.$inputTo = $('<input type="text" name="to" class="' + c4g.maps.constant.css.PLUGIN_DATETIMEPICKER_CLASS + '" placeholder="' + c4g.maps.constant.i18n.TRACKING_FILTER_TO_PLACEHOLDER + '">');
      this.$inputTo.focus(function(){
        $(this).removeClass(c4g.maps.constant.css.ERROR);
      });

      // submit button
      this.$submitButton = $('<button type="submit">' + c4g.maps.constant.i18n.TRACKING_FILTER_SUBMITBUTTON + '</button>');

      // append items to input-wrapper
      this.$inputFrom.appendTo(this.$contentDiv);
      //$('<br>').appendTo($contentDiv);
      this.$inputTo.appendTo(this.$contentDiv);
      //$('<br>').appendTo($contentDiv);
      this.$submitButton.appendTo(this.$contentDiv);

      // register button interaction
      this.$submitButton.click(function(event){
        event.preventDefault();
        self.handleRequests();

      });

      // append input wrapper to filter wrapper
      contentWrapper.appendChild(this.contentDiv);

      this.$contentWrapper = $(contentWrapper);

      this.$contentWrapper.addClass(c4g.maps.constant.css.HIDE);

      // append filter wrapper into current starboard
      this.$contentWrapper.insertAfter(self.layerswitcher.contentDiv);

      // initialize datepicker
      jQuery('.' + c4g.maps.constant.css.PLUGIN_DATETIMEPICKER_CLASS).appendDtpicker(self.dateTimePickerOptions);
      */
      var self,
          contentWrapper,
          contentHeadline,
          contentInfo;

      self = this;

      self.activeLayerFilter = {};
      self.filterLayerReference = {};

      contentWrapper = document.createElement('div');

      contentHeadline = document.createElement('h4');
      contentHeadline.innerHTML = c4g.maps.constant.i18n.TRACKING_FILTER_TITLE;
      contentWrapper.appendChild(contentHeadline);

      this.contentDiv = document.createElement('div');
      this.contentDiv.className = c4g.maps.constant.css.STARBOARD_CONTENT_FILTER + ' ' + c4g.maps.constant.css.STARBOARD_FILTER_INPUT_WRAPPER;
      contentInfo = document.createElement('p');
      this.contentDiv.appendChild(contentInfo);
      contentWrapper.appendChild(this.contentDiv);

      self.view = self.layerswitcher.starboard.addView({
        name: 'layerswitcher',
        triggerConfig: {
          // @TODO: Check
          // tipLabel: c4g.maps.constant.i18n.STARBOARD_VIEW_TRIGGER_LAYERSWITCHER,
          tipLabel: c4g.maps.constant.i18n.STARBOARD_VIEW_TRIGGER_FILTER,
          className: c4g.maps.constant.css.STARBOARD_VIEW_TRIGGER_FILTER
        },
        sectionElements: [
          {section: self.layerswitcher.starboard.contentContainer, element: contentWrapper},
          {section: self.layerswitcher.starboard.bottomToolbar, element: self.layerswitcher.starboard.viewTriggerBar}
        ],
        deactivateFunction: function() {
          self.hideAllFilterLayer();
        }
      });

      this.$contentDiv = $(this.contentDiv);

      self.dateTimePickerOptions = {
        'dateFormat': "DD.MM.YYYY hh:mm",
        'locale': "de",
        'firstDayOfWeek': 1,
        'closeOnSelected': true,
        'autodateOnStart': false
      };

      // from input
      this.$infoText = $('<p>'+c4g.maps.constant.i18n.TRACKING_FILTER_TEXT+'</p>');

      // from input
      this.$inputFrom = $('<input type="text" name="from" class="' + c4g.maps.constant.css.PLUGIN_DATETIMEPICKER_CLASS + '" placeholder="' + c4g.maps.constant.i18n.TRACKING_FILTER_FROM_PLACEHOLDER + '">');
      this.$inputFrom.focus(function(){
        $(this).removeClass(c4g.maps.constant.css.ERROR);
      });

      // to input
      this.$inputTo = $('<input type="text" name="to" class="' + c4g.maps.constant.css.PLUGIN_DATETIMEPICKER_CLASS + '" placeholder="' + c4g.maps.constant.i18n.TRACKING_FILTER_TO_PLACEHOLDER + '">');
      this.$inputTo.focus(function(){
        $(this).removeClass(c4g.maps.constant.css.ERROR);
      });

      // submit button
      this.$submitButton = $('<button type="submit">' + c4g.maps.constant.i18n.TRACKING_FILTER_SUBMITBUTTON + '</button>');

      this.$infoText.appendTo(this.$contentDiv);
      // append items to input-wrapper
      this.$inputFrom.appendTo(this.$contentDiv);
      //$('<br>').appendTo($contentDiv);
      this.$inputTo.appendTo(this.$contentDiv);
      //$('<br>').appendTo($contentDiv);
      this.$submitButton.appendTo(this.$contentDiv);

      // register button interaction
      this.$submitButton.click(function(event){
        event.preventDefault();
        self.handleRequests();

      });

      // append input wrapper to filter wrapper
      //contentWrapper.appendChild(this.contentDiv);

      //this.$contentWrapper = $(contentWrapper);

      //this.$contentWrapper.addClass(c4g.maps.constant.css.HIDE);

      // append filter wrapper into current starboard
      //this.$contentWrapper.insertAfter(self.layerswitcher.contentDiv);

      // initialize datepicker
      jQuery('.' + c4g.maps.constant.css.PLUGIN_DATETIMEPICKER_CLASS, this.$contentDiv).appendDtpicker(self.dateTimePickerOptions);

    },

    checkDateTimeInput: function(varValue, opt_$field) {

      var returnString,
          pattern,
          eplodedVars = {};

      pattern = /^\d{2}.\d{2}.\d{4}( \d{2}:\d{2})?$/g;

      if (varValue.match(pattern)) {
        // Format scheint zu stimmen
        if (varValue.indexOf(' ') > -1) {
          // Datum mit Zeit
          eplodedVars.arrDateTimeExploded = varValue.split(' ');
          eplodedVars.arrDateExploded = eplodedVars.arrDateTimeExploded[0].split('.');
          eplodedVars.arrTimeExploded = eplodedVars.arrDateTimeExploded[1].split(':');
          returnString = (new Date(eplodedVars.arrDateExploded[2], (eplodedVars.arrDateExploded[1]-1), eplodedVars.arrDateExploded[0], eplodedVars.arrTimeExploded[0], eplodedVars.arrTimeExploded[1], 0).getTime()/1000);
          return returnString;
        } else {
          //Datum ohne Zeit. Wir rechnen mit 00:00 als Zeit weiter
          eplodedVars.arrDateExploded = varValue.split('.');
          returnString = (new Date(eplodedVars.arrDateExploded[2], (eplodedVars.arrDateExploded[1]-1), eplodedVars.arrDateExploded[0], 0, 0, 0).getTime()/1000);
          return returnString;
        }

      } else {
        // Falsches Datum-Zeit-Format

        if (opt_$field && opt_$field.length) {
          opt_$field.addClass(c4g.maps.constant.css.ERROR)
        }

        return false;
      }

    },

    handleRequests: function() {

      var self = this,
          from = 0,
          to = 0,
          hasErrors = false,
          varRequestParams = {
            'filter': '',
            'structParams': {}
          };

      self.hideAllFilterLayer();

      if (self.$inputFrom.val()) {
        from = self.checkDateTimeInput(self.$inputFrom.val(), self.$inputFrom)
        if (typeof from === "number") {
          varRequestParams['filter'] += "&filterFrom=" + from;
        } else {
          hasErrors = true;
        }
      }

      if (self.$inputTo.val()) {
        to = self.checkDateTimeInput(self.$inputTo.val(), self.$inputTo);
        if (typeof to === "number") {
          varRequestParams['filter'] += "&filterTo=" + to;
        } else {
          hasErrors = true;
        }
      }

      self.getFilterableLayers();

      if (!hasErrors) {
        for (var key in self.activeLayerFilter) {
          if (self.activeLayerFilter.hasOwnProperty(key)) {

            varRequestParams['structParams'] = self.activeLayerFilter[key]
            self.doRequest(key, varRequestParams);

          }
        }
      }

    },

    doRequest: function(id, varRequestParam) {

      var self,
          uid,
          testObject;

      self = this;

      testObject = {
        content: [
          {
            data: {
              url: apiBaseUrl + "/trackingService?method=getBoxTrack" + varRequestParam['structParams']['urlParam'] + (varRequestParam['filter'] ? varRequestParam['filter'] : '')
            },
            format: "GeoJSON",
            locationStyle: varRequestParam['structParams']['locationStyle'] ? varRequestParam['structParams']['locationStyle'] : "1",
            settings: {
              crossOrigine: false,
              loadAsnyc: true,
              refresh: false,
              fitToExtend: true
            },
            type: 'urlData'
          }
        ]
      };

      uid = id;

      c4g.maps.layers[uid] = testObject;

      self.layerswitcher.proxy.checkLocationStyles();

      window.setTimeout(function() {
        self.layerswitcher.proxy.loadLayerContent(uid);
        self.layerswitcher.proxy.activeLayerIds[uid] = "visible";
      }, 300);

      self.filterLayerReference[uid] = testObject;

    },

    hideAllFilterLayer: function() {

      var self = this;

      for (var key in self.filterLayerReference) {
        if (self.filterLayerReference.hasOwnProperty(key)) {
          self.layerswitcher.proxy.hideLayer(key);
        }
      };

    },

    getFilterableLayers: function() {
      var self = this;

      self.activeLayerFilter = {};

      for (var key in self.layerswitcher.proxy.activeLayerIds) {
        if (self.layerswitcher.proxy.activeLayerIds.hasOwnProperty(key)) {
          if (c4g.maps.layers[key] && c4g.maps.layers[key].filterable) {
            //console.log(c4g.maps.layers[key]);
            self.activeLayerFilter['filter_'+key] = c4g.maps.layers[key].filterable;
          }
        }
      }
    },

    changeFromLayer: function(id, itemFilterParams) {

      var self = this;

      if (self.activeLayerFilter[id]) {
        self.layerswitcher.proxy.hideLayer(id);
        delete self.activeLayerFilter[id];
      } else {
        self.activeLayerFilter[id] = itemFilterParams;
      }

      if (Object.keys(self.activeLayerFilter).length > 0) {
        self.$contentWrapper.removeClass(c4g.maps.constant.css.HIDE);
      } else {
        self.hideAllFilterLayer();
        self.$contentWrapper.addClass(c4g.maps.constant.css.HIDE);
        //self.hide();
      }
    }

  });

}(jQuery, this.c4g));