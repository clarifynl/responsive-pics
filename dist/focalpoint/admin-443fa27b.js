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
(window.wpackioresponsivePicsfocalpointJsonp=window.wpackioresponsivePicsfocalpointJsonp||[]).push([[0],[function(t,i,e){e(1),e(2),t.exports=e(3)},function(t,i,e){var n="responsivePicsdist".replace(/[^a-zA-Z0-9_-]/g,"");e.p=window["__wpackIo".concat(n)]},function(t,i){var e;(e=jQuery)(document).ready((function(){var t,i,n,o,a=function(t){i.addClass("is-dragging")},c=function(t){console.log("draggingFocalPoint",t.target)},p=function(t){i.removeClass("is-dragging")},s=function(t){t.stopPropagation(),t.preventDefault()},d=function(t){t.stopPropagation(),t.preventDefault(),console.log("dropFocalPoint",o.position())},l=function(a){var c=wp.media.template("attachment-select-focal-point"),p=a.find(".thumbnail"),s=a.find(".details-image");console.log(s.width(),s.height()),s.on("load",(function(t){console.log(t,t.width(),t.height(),e(t.currentTarget).width(),e(t.currentTarget).height())})),c&&(p.prepend(c),i=a.find(".image-focal"),n=a.find(".image-focal__wrapper"),o=a.find(".image-focal__point"),a.find(".image-focal__clickarea"),s.prependTo(n),t=n.find(".details-image"));var d=wp.media.template("attachment-save-focal-point"),l=a.find(".attachment-actions");d&&l.append(d)},r=function(i){var l,r,f=i.get("compat");if(f.item){var g=e(f.item).find(".compat-field-responsive_pics_focal_point_x input").val(),h=e(f.item).find(".compat-field-responsive_pics_focal_point_y input").val();l=g,r=h,console.log(t,t.width(),t.height()),n.css({width:t.width(),height:t.height()}),o.css({left:"".concat(l,"%"),top:"".concat(r,"%"),display:"block"}),n.on("dragover",s),n.on("drop",d),o.on("dragstart",a),o.on("drag",c),o.on("dragend",p)}},f=wp.media.view.Attachment.Details.TwoColumn;wp.media.view.Attachment.Details.TwoColumn=f.extend({initialize:function(){this.model.on("change:compat",this.change,this)},render:function(){wp.media.view.Attachment.prototype.render.apply(this,arguments);var t=this.model.attributes.type;return"image"===t&&(l(this.$el),r(this.model)),this},change:function(){"image"===this.model.attributes.type&&r(this.model)}})}))},function(t,i,e){}],[[0,1]]]);
//# sourceMappingURL=admin-443fa27b.js.map