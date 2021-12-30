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
(window.wpackioresponsivePicsfocalpointJsonp=window.wpackioresponsivePicsfocalpointJsonp||[]).push([[0],[function(t,n,i){i(1),i(2),t.exports=i(3)},function(t,n,i){var e="responsivePicsdist".replace(/[^a-zA-Z0-9_-]/g,"");i.p=window["__wpackIo".concat(e)]},function(t,n){var i;(i=jQuery)(document).ready((function(){var t,n,e,o={width:0,height:0},a=function(i){var o=wp.media.template("attachment-select-focal-point"),a=i.find(".thumbnail"),c=i.find(".details-image");o&&(a.prepend(o),i.find(".image-focal"),n=i.find(".image-focal__wrapper"),e=i.find(".image-focal__point"),i.find(".image-focal__clickarea"),c.prependTo(n),t=n.find(".details-image"));var p=wp.media.template("attachment-save-focal-point"),r=i.find(".attachment-actions");p&&r.append(p)},c=function(t){var n=t.get("compat");if(n.item)return{x:i(n.item).find(".compat-field-responsive_pics_focal_point_x input").val(),y:i(n.item).find(".compat-field-responsive_pics_focal_point_y input").val()}},p=function(t,n){console.log(t,n),e.css({left:"".concat(t,"%"),top:"".concat(n,"%"),display:"block"})},r=function(t){i("body").addClass("focal-point-dragging"),t.originalEvent.dataTransfer.effectAllowed="move"},d=function(t){i("body").removeClass("focal-point-dragging")},s=function(t){t.stopPropagation(),t.preventDefault(),t.originalEvent.dataTransfer.dropEffect="move"},l=function(t){t.stopPropagation(),t.preventDefault();var n,i=(n=e.position(),console.log("calculateFocalPoint",n,o),{x:100*Math.round(n.x/o.width),y:100*Math.round(n.y/o.height)});p(i.x,i.y)},f=function(t){console.log("updateFocusInterface",t),o={width:t.width(),height:t.height()},n.css({width:"".concat(o.width,"px"),height:"".concat(o.height,"px")})},h=function(o){var a=c(o);p(a.x,a.y),t.on("load",(function(t){return f(i(t.currentTarget))})),i(window).on("resize",(function(){return f(t)})),n.on("dragover",s),n.on("drop",l),e.on("dragstart",r),e.on("dragend",d)},u=wp.media.view.Attachment.Details.TwoColumn;wp.media.view.Attachment.Details.TwoColumn=u.extend({initialize:function(){this.model.on("change:compat",this.change,this)},render:function(){wp.media.view.Attachment.prototype.render.apply(this,arguments);var t=this.model.attributes.type;return"image"===t&&(a(this.$el),h(this.model)),this},change:function(){if("image"===this.model.attributes.type){var t=c(this.model);p(t.x,t.y)}}})}))},function(t,n,i){}],[[0,1]]]);
//# sourceMappingURL=admin-56e15528.js.map