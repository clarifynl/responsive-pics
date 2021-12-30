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
(window.wpackioresponsivePicsfocalpointJsonp=window.wpackioresponsivePicsfocalpointJsonp||[]).push([[0],[function(i,t,e){e(1),e(2),i.exports=e(3)},function(i,t,e){var a="responsivePicsdist".replace(/[^a-zA-Z0-9_-]/g,"");e.p=window["__wpackIo".concat(a)]},function(i,t){var e;(e=jQuery)(document).ready((function(){var i,t,a=function(e){var a=wp.media.template("attachment-select-focal-point"),n=e.find(".thumbnail"),o=e.find(".details-image");a&&(n.prepend(a),e.find(".image-focal"),i=e.find(".image-focal__wrapper"),t=e.find(".image-focal__point"),e.find(".image-focal__clickarea"),o.prependTo(i));var c=wp.media.template("attachment-save-focal-point"),p=e.find(".attachment-actions");c&&p.append(c)},n=function(i){i.id;var a,n,o=i.get("compat");if(o.item){var c=e(o.item).find(".compat-field-responsive_pics_focal_point_x input").val(),p=e(o.item).find(".compat-field-responsive_pics_focal_point_y input").val();a=c,n=p,t.css({left:"".concat(a,"%"),top:"".concat(n,"%"),display:"block"})}},o=wp.media.view.Attachment.Details.TwoColumn;wp.media.view.Attachment.Details.TwoColumn=o.extend({initialize:function(){this.model.on("change:compat",this.change,this)},render:function(){wp.media.view.Attachment.prototype.render.apply(this,arguments);var i=this.model.attributes.type;return"image"===i&&(a(this.$el),n(this.model)),this},change:function(){"image"===this.model.attributes.type&&n(this.model)}})}))},function(i,t,e){}],[[0,1]]]);
//# sourceMappingURL=admin-b093087f.js.map