(function() {
  var template = Handlebars.template, templates = Handlebars.templates = Handlebars.templates || {};
templates['artist'] = template({"1":function(depth0,helpers,partials,data) {
  return " hidden";
  },"3":function(depth0,helpers,partials,data) {
  var stack1, helper, lambda=this.lambda, escapeExpression=this.escapeExpression, functionType="function", helperMissing=helpers.helperMissing;
  return "                <li data-id=\""
    + escapeExpression(lambda((data && data.index), depth0))
    + "\">\n                    <a href=\""
    + escapeExpression(lambda(((stack1 = (depth0 != null ? depth0.shazam : depth0)) != null ? stack1.link : stack1), depth0))
    + "\"><img src=\""
    + escapeExpression(((helper = (helper = helpers.image || (depth0 != null ? depth0.image : depth0)) != null ? helper : helperMissing),(typeof helper === functionType ? helper.call(depth0, {"name":"image","hash":{},"data":data}) : helper)))
    + "\" /></a>\n                    <a href=\""
    + escapeExpression(lambda(((stack1 = (depth0 != null ? depth0.shazam : depth0)) != null ? stack1.link : stack1), depth0))
    + "\">"
    + escapeExpression(((helper = (helper = helpers.name || (depth0 != null ? depth0.name : depth0)) != null ? helper : helperMissing),(typeof helper === functionType ? helper.call(depth0, {"name":"name","hash":{},"data":data}) : helper)))
    + "</a>\n                </li>\n";
},"5":function(depth0,helpers,partials,data) {
  var stack1, lambda=this.lambda, escapeExpression=this.escapeExpression;
  return " ("
    + escapeExpression(lambda(((stack1 = (depth0 != null ? depth0.similar : depth0)) != null ? stack1.length : stack1), depth0))
    + ")";
},"7":function(depth0,helpers,partials,data) {
  var stack1, helper, options, functionType="function", helperMissing=helpers.helperMissing, blockHelperMissing=helpers.blockHelperMissing, buffer = "            <ul>\n";
  stack1 = ((helper = (helper = helpers.similar || (depth0 != null ? depth0.similar : depth0)) != null ? helper : helperMissing),(options={"name":"similar","hash":{},"fn":this.program(8, data),"inverse":this.noop,"data":data}),(typeof helper === functionType ? helper.call(depth0, options) : helper));
  if (!helpers.similar) { stack1 = blockHelperMissing.call(depth0, stack1, options); }
  if (stack1 != null) { buffer += stack1; }
  stack1 = ((helper = (helper = helpers.similar || (depth0 != null ? depth0.similar : depth0)) != null ? helper : helperMissing),(options={"name":"similar","hash":{},"fn":this.noop,"inverse":this.program(10, data),"data":data}),(typeof helper === functionType ? helper.call(depth0, options) : helper));
  if (!helpers.similar) { stack1 = blockHelperMissing.call(depth0, stack1, options); }
  if (stack1 != null) { buffer += stack1; }
  return buffer + "            </ul>\n";
},"8":function(depth0,helpers,partials,data) {
  var helper, lambda=this.lambda, escapeExpression=this.escapeExpression, functionType="function", helperMissing=helpers.helperMissing;
  return "                <li class=\""
    + escapeExpression(lambda((data && data.index), depth0))
    + "\">\n                    <a href=\""
    + escapeExpression(((helper = (helper = helpers.lastfm_link || (depth0 != null ? depth0.lastfm_link : depth0)) != null ? helper : helperMissing),(typeof helper === functionType ? helper.call(depth0, {"name":"lastfm_link","hash":{},"data":data}) : helper)))
    + "\"><img src=\""
    + escapeExpression(((helper = (helper = helpers.image_url || (depth0 != null ? depth0.image_url : depth0)) != null ? helper : helperMissing),(typeof helper === functionType ? helper.call(depth0, {"name":"image_url","hash":{},"data":data}) : helper)))
    + "\" /></a>\n                    <a href=\""
    + escapeExpression(((helper = (helper = helpers.lastfm_link || (depth0 != null ? depth0.lastfm_link : depth0)) != null ? helper : helperMissing),(typeof helper === functionType ? helper.call(depth0, {"name":"lastfm_link","hash":{},"data":data}) : helper)))
    + "\">"
    + escapeExpression(((helper = (helper = helpers.name || (depth0 != null ? depth0.name : depth0)) != null ? helper : helperMissing),(typeof helper === functionType ? helper.call(depth0, {"name":"name","hash":{},"data":data}) : helper)))
    + "</a>\n                </li>\n";
},"10":function(depth0,helpers,partials,data) {
  return "                <li>No similar artists found for this artist.</li>\n";
  },"12":function(depth0,helpers,partials,data) {
  return "            <img src=\"img/similar-loader.gif\" /> Searching...</div>\n";
  },"compiler":[6,">= 2.0.0-beta.1"],"main":function(depth0,helpers,partials,data) {
  var stack1, helper, functionType="function", helperMissing=helpers.helperMissing, escapeExpression=this.escapeExpression, lambda=this.lambda, blockHelperMissing=helpers.blockHelperMissing, buffer = "<div class=\"artist-row\" data-id=\""
    + escapeExpression(((helper = (helper = helpers._index || (depth0 != null ? depth0._index : depth0)) != null ? helper : helperMissing),(typeof helper === functionType ? helper.call(depth0, {"name":"_index","hash":{},"data":data}) : helper)))
    + "\">\n    <h2 class=\"artist-name\"><a href=\""
    + escapeExpression(lambda(((stack1 = (depth0 != null ? depth0.shazam : depth0)) != null ? stack1.link : stack1), depth0))
    + "\" target=\"_blank\">"
    + escapeExpression(((helper = (helper = helpers.name || (depth0 != null ? depth0.name : depth0)) != null ? helper : helperMissing),(typeof helper === functionType ? helper.call(depth0, {"name":"name","hash":{},"data":data}) : helper)))
    + "</a></h2>\n    <div class=\"artist-details\">\n        <div class=\"artist-detail-heading\"><a class=\"artist-songs-more\" href=\"javascript:void(0)\">Songs Tagged ("
    + escapeExpression(lambda(((stack1 = ((stack1 = (depth0 != null ? depth0.shazam : depth0)) != null ? stack1.songs : stack1)) != null ? stack1.length : stack1), depth0))
    + ")</a></div>\n        <div class=\"artist-songs";
  stack1 = helpers['if'].call(depth0, (depth0 != null ? depth0._songs_folded : depth0), {"name":"if","hash":{},"fn":this.program(1, data),"inverse":this.noop,"data":data});
  if (stack1 != null) { buffer += stack1; }
  buffer += "\">\n            <ul>\n";
  stack1 = blockHelperMissing.call(depth0, lambda(((stack1 = (depth0 != null ? depth0.shazam : depth0)) != null ? stack1.songs : stack1), depth0), {"name":"shazam.songs","hash":{},"fn":this.program(3, data),"inverse":this.noop,"data":data});
  if (stack1 != null) { buffer += stack1; }
  buffer += "            </ul>\n        </div>\n        <div class=\"artist-detail-heading\"><a class=\"artist-similar-more\" href=\"javascript:void(0)\">Similar Artists";
  stack1 = helpers['if'].call(depth0, (depth0 != null ? depth0._similar_loaded : depth0), {"name":"if","hash":{},"fn":this.program(5, data),"inverse":this.noop,"data":data});
  if (stack1 != null) { buffer += stack1; }
  buffer += "</a></div>\n        <div class=\"artist-similar";
  stack1 = helpers['if'].call(depth0, (depth0 != null ? depth0._similar_folded : depth0), {"name":"if","hash":{},"fn":this.program(1, data),"inverse":this.noop,"data":data});
  if (stack1 != null) { buffer += stack1; }
  buffer += "\">\n";
  stack1 = helpers['if'].call(depth0, (depth0 != null ? depth0._similar_loaded : depth0), {"name":"if","hash":{},"fn":this.program(7, data),"inverse":this.program(12, data),"data":data});
  if (stack1 != null) { buffer += stack1; }
  return buffer + "    </div>\n</div>";
},"useData":true});
templates['similar_artist'] = template({"1":function(depth0,helpers,partials,data) {
  var helper, functionType="function", helperMissing=helpers.helperMissing, escapeExpression=this.escapeExpression;
  return "    <li>"
    + escapeExpression(((helper = (helper = helpers.name || (depth0 != null ? depth0.name : depth0)) != null ? helper : helperMissing),(typeof helper === functionType ? helper.call(depth0, {"name":"name","hash":{},"data":data}) : helper)))
    + "</li>\n";
},"compiler":[6,">= 2.0.0-beta.1"],"main":function(depth0,helpers,partials,data) {
  var stack1, helper, options, functionType="function", helperMissing=helpers.helperMissing, blockHelperMissing=helpers.blockHelperMissing, buffer = "<ul>\n";
  stack1 = ((helper = (helper = helpers.similar_artists || (depth0 != null ? depth0.similar_artists : depth0)) != null ? helper : helperMissing),(options={"name":"similar_artists","hash":{},"fn":this.program(1, data),"inverse":this.noop,"data":data}),(typeof helper === functionType ? helper.call(depth0, options) : helper));
  if (!helpers.similar_artists) { stack1 = blockHelperMissing.call(depth0, stack1, options); }
  if (stack1 != null) { buffer += stack1; }
  return buffer + "</ul>";
},"useData":true});
})();