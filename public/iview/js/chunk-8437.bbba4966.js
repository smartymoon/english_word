(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["chunk-8437"],{"35b7":function(t,a,e){},dc64:function(t,a,e){"use strict";e.r(a);var r=function(){var t=this,a=t.$createElement,e=t._self._c||a;return e("div",[e("Card",{attrs:{shadow:""}},[e("Row",[e("i-col",{attrs:{span:"4"}},[e("Button",{on:{click:t.createTagParams}},[t._v("添加一个标签")])],1),e("i-col",{attrs:{span:"20"}},[e("p",[t._v("动态路由，添加params")])])],1)],1),e("Card",{staticStyle:{"margin-top":"10px"},attrs:{shadow:""}},[e("Row",[e("i-col",{attrs:{span:"4"}},[e("Button",{on:{click:t.createTagQuery}},[t._v("添加一个标签")])],1),e("i-col",{attrs:{span:"20"}},[e("p",[t._v("动态路由，添加query")])])],1)],1)],1)},s=[],n=e("c93e"),o=(e("cadf"),e("551c"),e("097d"),e("2f62")),c={name:"tools_methods_page",methods:Object(n["a"])({},Object(o["c"])(["addTag"]),{createTagParams:function(){var t=parseInt(1e5*Math.random()),a={name:"params",params:{id:t},meta:{title:"动态路由-".concat(t)}};this.addTag({route:a,type:"push"}),this.$router.push(a)},createTagQuery:function(){var t=parseInt(1e5*Math.random()),a={name:"query",query:{id:t},meta:{title:"参数-".concat(t)}};this.addTag({route:a,type:"push"}),this.$router.push(a)}})},u=c,i=(e("f5bd"),e("2877")),p=Object(i["a"])(u,r,s,!1,null,null,null);p.options.__file="tools-methods.vue";a["default"]=p.exports},f5bd:function(t,a,e){"use strict";var r=e("35b7"),s=e.n(r);s.a}}]);