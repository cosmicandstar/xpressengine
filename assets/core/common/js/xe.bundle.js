!function(e){function r(e,r,o){return 4===arguments.length?t.apply(this,arguments):void n(e,{declarative:!0,deps:r,declare:o})}function t(e,r,t,o){n(e,{declarative:!1,deps:r,executingRequire:t,execute:o})}function n(e,r){r.name=e,e in p||(p[e]=r),r.normalizedDeps=r.deps}function o(e,r){if(r[e.groupIndex]=r[e.groupIndex]||[],-1==v.call(r[e.groupIndex],e)){r[e.groupIndex].push(e);for(var t=0,n=e.normalizedDeps.length;n>t;t++){var a=e.normalizedDeps[t],u=p[a];if(u&&!u.evaluated){var d=e.groupIndex+(u.declarative!=e.declarative);if(void 0===u.groupIndex||u.groupIndex<d){if(void 0!==u.groupIndex&&(r[u.groupIndex].splice(v.call(r[u.groupIndex],u),1),0==r[u.groupIndex].length))throw new TypeError("Mixed dependency cycle detected");u.groupIndex=d}o(u,r)}}}}function a(e){var r=p[e];r.groupIndex=0;var t=[];o(r,t);for(var n=!!r.declarative==t.length%2,a=t.length-1;a>=0;a--){for(var u=t[a],i=0;i<u.length;i++){var s=u[i];n?d(s):l(s)}n=!n}}function u(e){return x[e]||(x[e]={name:e,dependencies:[],exports:{},importers:[]})}function d(r){if(!r.module){var t=r.module=u(r.name),n=r.module.exports,o=r.declare.call(e,function(e,r){if(t.locked=!0,"object"==typeof e)for(var o in e)n[o]=e[o];else n[e]=r;for(var a=0,u=t.importers.length;u>a;a++){var d=t.importers[a];if(!d.locked)for(var i=0;i<d.dependencies.length;++i)d.dependencies[i]===t&&d.setters[i](n)}return t.locked=!1,r},r.name);t.setters=o.setters,t.execute=o.execute;for(var a=0,i=r.normalizedDeps.length;i>a;a++){var l,s=r.normalizedDeps[a],c=p[s],v=x[s];v?l=v.exports:c&&!c.declarative?l=c.esModule:c?(d(c),v=c.module,l=v.exports):l=f(s),v&&v.importers?(v.importers.push(t),t.dependencies.push(v)):t.dependencies.push(null),t.setters[a]&&t.setters[a](l)}}}function i(e){var r,t=p[e];if(t)t.declarative?c(e,[]):t.evaluated||l(t),r=t.module.exports;else if(r=f(e),!r)throw new Error("Unable to load dependency "+e+".");return(!t||t.declarative)&&r&&r.__useDefault?r["default"]:r}function l(r){if(!r.module){var t={},n=r.module={exports:t,id:r.name};if(!r.executingRequire)for(var o=0,a=r.normalizedDeps.length;a>o;o++){var u=r.normalizedDeps[o],d=p[u];d&&l(d)}r.evaluated=!0;var c=r.execute.call(e,function(e){for(var t=0,n=r.deps.length;n>t;t++)if(r.deps[t]==e)return i(r.normalizedDeps[t]);throw new TypeError("Module "+e+" not declared as a dependency.")},t,n);c&&(n.exports=c),t=n.exports,t&&t.__esModule?r.esModule=t:r.esModule=s(t)}}function s(r){if(r===e)return r;var t={};if("object"==typeof r||"function"==typeof r)if(g){var n;for(var o in r)(n=Object.getOwnPropertyDescriptor(r,o))&&h(t,o,n)}else{var a=r&&r.hasOwnProperty;for(var o in r)(!a||r.hasOwnProperty(o))&&(t[o]=r[o])}return t["default"]=r,h(t,"__useDefault",{value:!0}),t}function c(r,t){var n=p[r];if(n&&!n.evaluated&&n.declarative){t.push(r);for(var o=0,a=n.normalizedDeps.length;a>o;o++){var u=n.normalizedDeps[o];-1==v.call(t,u)&&(p[u]?c(u,t):f(u))}n.evaluated||(n.evaluated=!0,n.module.execute.call(e))}}function f(e){if(D[e])return D[e];if("@node/"==e.substr(0,6))return y(e.substr(6));var r=p[e];if(!r)throw"Module "+e+" not present.";return a(e),c(e,[]),p[e]=void 0,r.declarative&&h(r.module.exports,"__esModule",{value:!0}),D[e]=r.declarative?r.module.exports:r.esModule}var p={},v=Array.prototype.indexOf||function(e){for(var r=0,t=this.length;t>r;r++)if(this[r]===e)return r;return-1},g=!0;try{Object.getOwnPropertyDescriptor({a:0},"a")}catch(m){g=!1}var h;!function(){try{Object.defineProperty({},"a",{})&&(h=Object.defineProperty)}catch(e){h=function(e,r,t){try{e[r]=t.value||t.get.call(e)}catch(n){}}}}();var x={},y="undefined"!=typeof System&&System._nodeRequire||"undefined"!=typeof require&&require.resolve&&"undefined"!=typeof process&&require,D={"@empty":{}};return function(e,n,o){return function(a){a(function(a){for(var u={_nodeRequire:y,register:r,registerDynamic:t,get:f,set:function(e,r){D[e]=r},newModule:function(e){return e}},d=0;d<n.length;d++)(function(e,r){r&&r.__esModule?D[e]=r:D[e]=s(r)})(n[d],arguments[d]);o(u);var i=f(e[0]);if(e.length>1)for(var d=1;d<e.length;d++)f(e[d]);return i.__useDefault?i["default"]:i})}}}("undefined"!=typeof self?self:global)

(["1"], [], function($__System) {

$__System.registerDynamic("1", [], true, function($__require, exports, module) {
  ;
  var define,
      global = this,
      GLOBAL = this;
  (function(exports) {
    'use strict';
    var self,
        _options;
    var XE = {
      initialize: initialize,
      setup: setup,
      configure: configure,
      cssLoad: cssLoad,
      toast: toast,
      toastByStatus: toastByStatus,
      formError: formError,
      formErrorClear: formErrorClear,
      validate: validate,
      getLocale: getLocale,
      getDefaultLocale: getDefaultLocale,
      options: {},
      Lang: '',
      Progress: '',
      Request: '',
      Component: ''
    };
    exports.XE = XE;
    return XE;
    function initialize(callback) {
      self = this;
      _options = {};
      _loadXEModule().promise().then(function() {
        callback();
      });
    }
    function _loadXEModule() {
      var d = $.Deferred();
      System.amdRequire(['xe.lang', 'xe.progress', 'xe.request', 'xe.component'], function(lang, progress, request, component) {
        self.Lang = lang;
        self.Progress = progress;
        self.Request = request;
        self.Component = component;
        self.ajax = self.Request.ajax = function(url, options) {
          if (typeof url === "object") {
            options = $.extend({}, self.Request.options, url);
            url = undefined;
          } else {
            options = $.extend({}, options, self.Request.options, {url: url});
            url = undefined;
          }
          $.ajaxSetup(options);
          var jqXHR = $.ajax(url, options);
          return jqXHR;
        };
        d.resolve();
      });
      return d;
    }
    function setup(options) {
      _options.loginUserId = options.loginUserId;
      self.options.loginUserId = options.loginUserId;
      self.Request.setup({headers: {'X-CSRF-TOKEN': options['X-CSRF-TOKEN']}});
    }
    function configure(options) {
      $.extend(_options, options);
      $.extend(self.options, options);
    }
    function cssLoad(url) {
      $('head').append($('<link>').attr('rel', 'stylesheet').attr('href', url));
    }
    function toast(type, message) {
      if (type == '') {
        type = 'danger';
      }
      System.import('xecore:/common/js/modules/griper/griper').then(function(griper) {
        return griper.toast(type, message);
      });
    }
    function toastByStatus(status, message) {
      System.import('xecore:/common/js/modules/griper/griper').then(function(griper) {
        return griper.toast(griper.toast.fn.statusToType(status), message);
      });
    }
    function formError($element, message) {
      System.import('xecore:/common/js/modules/griper/griper').then(function(griper) {
        return griper.form($element, message);
      });
    }
    function formErrorClear($form) {
      System.import('xecore:/common/js/modules/griper/griper').then(function(griper) {
        return griper.form.fn.clear($form);
      });
    }
    function validate($form) {
      System.import('xecore:/common/js/modules/validator').then(function(validator) {
        validator.validate($form);
      });
    }
    function getLocale() {
      return _options.locale;
    }
    function getDefaultLocale() {
      return _options.defaultLocale;
    }
    if (this.Request) {}
  })(window);
  return module.exports;
});

})
(function(factory) {
  factory();
});