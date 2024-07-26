import app from 'flarum/forum/app';
import {extend} from 'flarum/common/extend';
import DiscussionListItem from 'flarum/components/DiscussionListItem';

export const modifyPosts = () => {


  extend(DiscussionListItem.prototype, 'infoItems', function (items) {

    const firstPost = this.attrs.discussion.firstPost();


    if (firstPost) {
      let mediaPreviewCache = app.forum.attribute('mediaPreviewCache');
      try {
        mediaPreviewCache = JSON.parse(mediaPreviewCache);
        const excerpt = mediaPreviewCache[firstPost.data.id];
        if (excerpt) {
          items.add('excerpt', m.trust(excerpt), -100);
        }
      } catch (e) {
      }
    }
  });

  /*
  extend(DiscussionListItem.prototype, 'infoItems', function (items) {
    // Add extra HTML to the `DiscussionListItem-info` class
    //const extraHtml = '<div class="extra-info">Your extra HTML here</div>';
    //items.add('extra-html', extraHtml, 10);
  });
  */

}
