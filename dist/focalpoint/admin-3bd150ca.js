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
(window.wpackioresponsivePicsfocalpointJsonp=window.wpackioresponsivePicsfocalpointJsonp||[]).push([[0],[function(t,i,e){e(1),e(2),t.exports=e(3)},function(t,i,e){var n="responsivePicsdist".replace(/[^a-zA-Z0-9_-]/g,"");e.p=window["__wpackIo".concat(n)]},function(t,i){var e;(e=jQuery)(document).ready((function(){var t,i,n,a={width:0,height:0},o=function(t){e("body").addClass("focal-point-dragging"),t.originalEvent.dataTransfer.effectAllowed="move"},c=function(t){e("body").removeClass("focal-point-dragging")},p=function(t){t.stopPropagation(),t.preventDefault(),t.originalEvent.dataTransfer.dropEffect="move"},d=function(t){t.stopPropagation(),t.preventDefault(),console.log("dropFocalPoint",n.position())},r=function(e){var a=wp.media.template("attachment-select-focal-point"),o=e.find(".thumbnail"),c=e.find(".details-image");a&&(o.prepend(a),e.find(".image-focal"),i=e.find(".image-focal__wrapper"),n=e.find(".image-focal__point"),e.find(".image-focal__clickarea"),c.prependTo(i),t=i.find(".details-image"));var p=wp.media.template("attachment-save-focal-point"),d=e.find(".attachment-actions");p&&d.append(p)},s=function(r){var s,l,f=r.get("compat");if(f.item){var m=e(f.item).find(".compat-field-responsive_pics_focal_point_x input").val(),h=e(f.item).find(".compat-field-responsive_pics_focal_point_y input").val();s=m,l=h,t.on("load",(function(t){a={width:e(t.currentTarget).width(),height:e(t.currentTarget).height()},i.css({width:"".concat(a.width,"px"),height:"".concat(a.height,"px")})})),n.css({left:"".concat(s,"%"),top:"".concat(l,"%"),display:"block"}),i.on("dragover",p),i.on("drop",d),n.on("dragstart",o),n.on("dragend",c)}},l=wp.media.view.Attachment.Details.TwoColumn;wp.media.view.Attachment.Details.TwoColumn=l.extend({initialize:function(){this.model.on("change:compat",this.change,this)},render:function(){wp.media.view.Attachment.prototype.render.apply(this,arguments);var t=this.model.attributes.type;return"image"===t&&(r(this.$el),s(this.model)),this},change:function(){"image"===this.model.attributes.type&&s(this.model)}})}))},function(t,i,e){}],[[0,1]]]);
//# sourceMappingURL=admin-3bd150ca.js.map