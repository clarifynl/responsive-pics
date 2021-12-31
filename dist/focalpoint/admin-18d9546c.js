/*!
 * 
 * ResponsivePics
 * 
 * @author Booreiland
 * @version 1.4.0
 * @link https://responsive.pics
 * @license undefined
 * 
 * Copyright (c) 2021 Booreiland
 * 
 * This software is released under the [MIT License](https://github.com/booreiland/responsive-pics/blob/master/LICENSE)
 */
(window.wpackioresponsivePicsfocalpointJsonp=window.wpackioresponsivePicsfocalpointJsonp||[]).push([[0],[function(t,e,i){i(1),i(2),t.exports=i(3)},function(t,e,i){var n="responsivePicsdist".replace(/[^a-zA-Z0-9_-]/g,"");i.p=window["__wpackIo".concat(n)]},function(t,e){var i;(i=jQuery)(document).ready((function(){var t,e,n,o=!1,a={width:0,height:0},c=function(i){var o=wp.media.template("attachment-select-focal-point"),a=i.find(".thumbnail"),c=i.find(".details-image");o&&(a.prepend(o),i.find(".image-focal"),e=i.find(".image-focal__wrapper"),n=i.find(".image-focal__point"),i.find(".image-focal__clickarea"),c.prependTo(e),t=e.find(".details-image"));var p=wp.media.template("attachment-save-focal-point"),s=i.find(".attachment-actions");p&&s.append(p)},p=function(t){var e=t.get("compat");if(e.item)return{x:i(e.item).find(".compat-field-responsive_pics_focal_point_x input").val(),y:i(e.item).find(".compat-field-responsive_pics_focal_point_y input").val()}},s=function(t,e){console.log("setFocalPoint",t,e),n.css({left:"".concat(t,"%"),top:"".concat(e,"%"),display:"block"})},l=function(t){o=!0,i("body").addClass("focal-point-dragging");var e=i(t.currentTarget).offset();console.log("x: ",t.pageX,t.originalEvent.clientX),console.log("y: ",t.pageY,t.originalEvent.clientY),e.left,t.pageX,e.top,t.pageY},r=function(t){t.preventDefault(),o&&console.log(t)},d=function(t){i("body").removeClass("focal-point-dragging"),o=!1},f=function(t){a={width:t.width(),height:t.height()},e.css({width:"".concat(a.width,"px"),height:"".concat(a.height,"px")})},m=function(e){var o=p(e);s(o.x,o.y),t.on("load",(function(t){return f(i(t.currentTarget))})),i(window).on("resize",(function(){return f(t)})),n.on("mousedown",l),n.on("mousemove",r),n.on("mouseup",d)},u=wp.media.view.Attachment.Details.TwoColumn;wp.media.view.Attachment.Details.TwoColumn=u.extend({initialize:function(){this.model.on("change:compat",this.change,this)},render:function(){wp.media.view.Attachment.prototype.render.apply(this,arguments);var t=this.model.attributes.type;return"image"===t&&(c(this.$el),m(this.model)),this},change:function(){if("image"===this.model.attributes.type){var t=p(this.model);s(t.x,t.y)}}})}))},function(t,e,i){}],[[0,1]]]);
//# sourceMappingURL=admin-18d9546c.js.map