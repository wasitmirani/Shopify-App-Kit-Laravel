import {Modal, TextField} from '@shopify/polaris';
import {useContext, useState} from 'react';
import {AppContext} from '../../context/AppProvider';
import ReviewService from '../../services/ReviewService';
import {showToast} from '../../utils/constant';

let handle = import.meta.env.VITE_SHOPIFY_APP_STORE_ICONITO_HANDLE;
const RateBanner = () => {
    const {user} = useContext(AppContext);
    const [showBanner, setShowBanner] = useState(true);
    const [rate, setRate] = useState('');
    const [comment, setComment] = useState('');

    const handleRate = (rate) => {
        if (rate === 5) {
            let url = `https://apps.shopify.com/${handle}#modal-show=WriteReviewModal`;
            window.open(url, '_blank');
            return;
        }
        setRate(rate);
    };

    const handleReviewSave = () => {
        ReviewService.submitReview(rate, comment).then((response) => {
            showToast('Review Saved Successfully.',{type:'success'});
            setRate('');
        }).catch((error) => {
            showToast(error.response.data.message);
        });
    };

    return (showBanner ?
        <div className="container-fluid rating_block">
            <Modal
                open={rate}
                title={'Add a comment'}
                primaryAction={{
                    content: 'Save',
                    onAction: () => handleReviewSave(),
                }}
                onClose={() => {
                    setRate('');
                    setComment('');
                }}
                secondaryActions={[
                    {
                        content: 'Close',
                        onAction: () => {
                            setRate('');
                            setComment('');
                        },
                    },
                ]}
            >
                <Modal.Section>
                    <div id={'rate-comment-image'}>
                        <img src={'/images/review_img.png'}/>
                    </div>
                    <div className={'rate-banner-input-wrapper'}>
                        <TextField
                            label={''}
                            placeholder={'What is your view?'}
                            multiline={4}
                            value={comment}
                            onChange={(value) => setComment(value)}
                        />
                    </div>
                </Modal.Section>
            </Modal>
            <div className="eco_content rating_section_wise">
                <div className="row">
                    <div
                        className="col-lg-9 col-md-8 col-sm-12"
                        style={{display: 'flex', alignContent: 'center'}}
                    >
                        <div className="d-flex">
                            <div
                                className="content_rating_"
                                style={{display: 'flex', alignItems: 'center'}}
                            >
                                <img
                                    src="/images/ic_heart.png"
                                    style={{
                                        width: 40,
                                        height: 40,
                                        margin: '0 10px',
                                    }}
                                />
                                <h6>
                                    We care deeply about your feedback. Let us
                                    know how we could do
                                    better.{' '}
                                    <span> <img src="/images/hand.png"/> </span>
                                </h6>
                            </div>
                        </div>
                    </div>
                    <div className="col-lg-3 col-md-4 col-sm-12">
                        <div className="rating_star">
                            <div className="rating">
                                <button
                                    type="button"
                                    className="close close_rating_tab"
                                    style={{width: 50}}
                                    onClick={() => setShowBanner(false)}
                                    data-dismiss="modal"
                                    aria-label="Close"
                                >
                                    <span aria-hidden="true" style={{
                                        fontSize: 25,
                                        color: '#000',
                                    }}> ×</span>
                                </button>
                                {
                                    Array.from([5, 4, 3, 2, 1]).
                                        map((star, index) => {
                                            return (
                                                <>
                                                    <input
                                                        type="radio"
                                                        name="rating"
                                                        defaultValue={star}
                                                        id={star}
                                                        className="rating_count"
                                                    />
                                                    <label
                                                        onClick={() => handleRate(
                                                            star)}
                                                        htmlFor={star}>☆</label>{' '}
                                                </>
                                            );
                                        })
                                }

                            </div>
                            {
                                user?.reviews_count > 0
                                    ?
                                    <div> (I already wrote a review)</div>
                                    : null
                            }
                        </div>
                    </div>
                </div>
            </div>
        </div>

        : null);

};

export default RateBanner;
