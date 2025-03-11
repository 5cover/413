import { requireElementById, scroller } from './util.js';

const review_form = requireElementById('review-form');
if (review_form?.getElementsByClassName('message').item(0).childElementCount > 0) {
    scroller(review_form);
}
