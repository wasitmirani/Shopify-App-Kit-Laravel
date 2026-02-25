import {
    Box,
    Button,
    DropZone,
    Icon,
    LegacyStack,
    Modal,
    Scrollable,
    Text,
    TextField,
} from '@shopify/polaris';
import {StarFilledMinor, UploadMajor} from '@shopify/polaris-icons';
import $ from 'jquery';
import React, {useContext, useEffect, useState} from 'react';
import {toast} from 'react-toastify';
import {AppContext} from '../../context/AppProvider';
import IconService from '../../services/IconService';
import {
    getCategoryByIndex,
    getNext3DIconCategory,
    getNextRegularIconCategory, showToast,
} from '../../utils/constant';

let is_processing = false;
let icon_host = import.meta.env.VITE_CLOUDFRONT_ICON_HOST;
let is_calling = false;
let is_searched = false;
let timeout = 0;

const IconModal = ({onClose, setIcon, _loading, showUpgradePlanBanner}) => {
    const [iconType, setIconType] = useState('regular');
    const [loading, setLoading] = useState(false);
    const [categories, setCategories] = useState([]);
    const [customIcons, setCustomIcons] = useState([]);
    const [search, setSearch] = useState('');
    const {plan} = useContext(AppContext);

    useEffect(() => {
        if(plan.upload_custom_icons){
            getCustomIcons();
        }
        $('#icon-modal-section-wrapper').parent().css('overflow-y', 'hidden');
    }, []);

    const getCustomIcons = () => {
        IconService.getCustomIcons().then(async (response) => {
            if (response.data?.data) {
                let _icons = response.data.data.map((icon, indx) => {
                    let url = icon_host + icon.url.split('.com')[1];
                    return {
                        'name': icon.name,
                        'id': icon.id,
                        'type': 'custom',
                        'src': icon.url,
                    };
                });
                await setCustomIcons(_icons);
            }
        }).catch((error) => {
        }).finally(() => {

        });
    };

    useEffect(() => {
        if (categories.length === 0) {
            if (search) {
                getSearchIcons();
            } else {
                if (iconType === 'regular') {
                    getRegularIconsByCategory('ecommerce', 'Ecommerce');
                } else {
                    getRegularIconsByCategory('brands_and_social_medias',
                        'Brands and social medias');
                }
            }
        }
    }, [categories]);

    useEffect(async () => {
        await setCategories([]);
    }, [iconType]);

    const getRegularIconsByCategory = (category, name = '') => {
        if(!is_calling) {
            is_calling = !is_calling;
            IconService.getRegularIconsByCategory(category, iconType, search).
                then((response) => {
                    if (response.data?.data) {
                        let cats = [...categories];
                        let _icons = response.data.data.map((icon, indx) => {
                            let url = icon_host + icon.url.split('.com')[1];
                            return {
                                'name': icon.name,
                                'id': icon.id,
                                'type': 'app-icon',
                                'src': url,
                            };
                        });
                        if (_icons.length > 0) {

                            cats.push({name: name ?? category, icons: _icons});
                            setCategories(cats);
                        }
                    }
                }).
                catch((error) => {
                }).
                finally(() => {
                    is_processing = false;
                    is_calling = !is_calling;
                });
        }
    };

    const handleIconsScroll = async (e) => {
        let cat = categories[categories.length - 1]?.name;
        let next_cat = iconType === 'regular'
            ? getNextRegularIconCategory(cat)
            : getNext3DIconCategory(cat);
        if (next_cat && search === '' && is_processing === false) {
            is_processing = true;
            getRegularIconsByCategory(next_cat[0], next_cat[1]);
        }
    };

    const handleDropZoneDrop = (_dropFiles) => {
        let formdata = new FormData();
        formdata.append('icon', _dropFiles[0]);
        IconService.uploadIcon(formdata).then((response) => {
            if (response.data?.data) {
                let custom_icons = [...customIcons];
                let i = response.data.data;

                custom_icons.push({
                    'name': i.name,
                    'id': i.id,
                    'type': 'custom',
                    'src': i.url,
                });
                setCustomIcons(custom_icons);
            }
        }).catch((error) => {
        }).finally(() => {

        });
    };

    useEffect(() => {
        // getSearchIcons();
        if (!timeout) {
            timeout = 300;
        }

        const delayDebounceFn = setTimeout(async function() {
            if (search) {
                // search and set items
                await IconService.getSearchIconsByCategory(iconType, search).
                    then((response) => {
                        if (response.data?.data) {
                            let cats = [];
                            let _icons = Object.entries(response.data.data).
                                map((cat, index) => {
                                    let icons = cat[1].map((icon, index) => {
                                        let url = icon_host +
                                            icon.url.split('.com')[1];
                                        return {
                                            'name': icon.name,
                                            'id': icon.id,
                                            'type': 'app-icon',
                                            'src': url,
                                        };
                                    });
                                    let cat_name = getCategoryByIndex(cat[0]);

                                    cats.push({name: cat_name, icons: icons});
                                });
                            setCategories(cats);
                        }
                    }).
                    catch((error) => {
                    }).
                    finally(() => {

                    });
            } else {
                if (is_searched === false){
                    setCategories([]);
                }
            }
        }, timeout);

        return () => clearTimeout(delayDebounceFn);
        is_searched = true;
    }, [search]);

    const getSearchIcons = async () => {
        if (search) {
            // search and set items
            await IconService.getSearchIconsByCategory(iconType, search).
                then((response) => {
                    if (response.data?.data) {
                        let cats = [];
                        let _icons = Object.entries(response.data.data).
                            map((cat, index) => {
                                let icons = cat[1].map((icon, index) => {
                                    let url = icon_host +
                                        icon.url.split('.com')[1];
                                    return {
                                        'name': icon.name,
                                        'id': icon.id,
                                        'type': 'app-icon',
                                        'src': url,
                                    };
                                });
                                let cat_name = getCategoryByIndex(cat[0]);

                                cats.push({name: cat_name, icons: icons});
                            });
                        setCategories(cats);
                    }
                }).
                catch((error) => {
                }).
                finally(() => {

                });
        } else {
            setCategories([]);
        }
    };

    const handleFileUploadAccess = () => {
        if(plan.upload_custom_icons !== 1){
            showToast('For Premium features, please upgrade your plan.',{type:'info'})
            showUpgradePlanBanner();
        }
    }

    const handle3DIconAccess = () => {
        if(plan['3d_icon']){
            setIconType('3d');
        }else{
            showToast('For Premium features, please upgrade your plan.',{type:'info'})
            showUpgradePlanBanner();
        }
    }

    return (
        <Modal
            open={true}
            large
            title={'Choose Icon'}
            onClose={() => onClose()}
        >
            <div id={'icon-modal-section-wrapper'}>
                <Modal.Section>
                    <div className={'icon-modal-header'}>
                    <LegacyStack distribution={'fillEvenly'}
                                 alignment={'center'}>
                        <TextField
                            id={'search'}
                            name={'search'}
                            value={search}
                            onChange={(value) => setSearch(value)}
                            placeholder={'Search Icon'}
                            label={''}
                        />
                        {
                                <DropZone variableHeight={true} type={'image'}
                                          onDrop={handleDropZoneDrop}
                                          disabled={plan.upload_custom_icons !== 1}
                                          outline={false}>
                                    <Button onClick={()=> handleFileUploadAccess()} id={'icon-modal-upload-btn'} primary>
                                        <LegacyStack>
                                            <Icon
                                                source={UploadMajor}
                                                color="warning"
                                            />
                                            <Text> Upload Icon</Text>
                                        </LegacyStack>
                                    </Button>
                                </DropZone>
                        }

                    </LegacyStack>
                    {
                            <div className="icon-modal-tab-btn">
                                <div>
                                    <Button primary={iconType === 'regular'}
                                            onClick={() => setIconType('regular')}>
                                        Regular
                                    </Button>

                                    <Button primary={iconType === '3d'}
                                            onClick={() => handle3DIconAccess()}>
                                        <LegacyStack distribution={''}>
                                            <Text> 3D Icon ⭐</Text>
                                        </LegacyStack>
                                    </Button>
                                </div>
                            </div>
                    }
                    </div>
                    <Scrollable horizontal={false} shadow
                                style={{height: '550px'}}
                                onScrolledToBottom={() => handleIconsScroll()}>

                        {
                            plan.upload_custom_icons && customIcons.length > 0 ?
                                <>
                                    <Text id={'your-upload-title'}
                                          variant={'headingMd'}> Yours
                                        Upload </Text>
                                    <Box className={'modal-icons-wrapper'}
                                         padding={2}>
                                        {
                                            customIcons?.map((icon, index) => {
                                                return (
                                                    <div key={index}
                                                         className="col-md-2 form-group">
                                                            <p>{JSON.stringify(icon)}</p>
                                                        <div
                                                            className="tb_icon_image">
                                                            <img
                                                                className="icon_img"
                                                                onClick={() => setIcon(
                                                                    icon)}
                                                                src={icon.src}/>
                                                        </div>
                                                    </div>
                                                );
                                            })
                                        }

                                    </Box>
                                </>
                                : null
                        }

                        <div className={'category-wise-icon-wrapper'}>
                            {
                                categories?.map((cat, index) => {
                                    return (
                                        <div key={index}>
                                            <Text id={'your-upload-title'}
                                                  variant={'headingMd'}> {cat.name} </Text>
                                            <Box
                                                className={'modal-icons-wrapper'}
                                                padding={2}>
                                                {
                                                    cat.icons.map((icon, i) => {
                                                        return (
                                                            <div key={i}
                                                                 className="col-md-2 form-group">
                                                                <div
                                                                    className="tb_icon_image">
                                                                    <img
                                                                        className="icon_img"
                                                                        onClick={() => setIcon(
                                                                            icon)}
                                                                        src={icon.src}/>
                                                                </div>
                                                            </div>
                                                        );
                                                    })
                                                }
                                            </Box>
                                        </div>
                                    );
                                })
                            }
                        </div>

                    </Scrollable>
                </Modal.Section>
            </div>
        </Modal>
    );
};

export default IconModal;
