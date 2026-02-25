import {
    Box,
    Button,
    ButtonGroup,
    LegacyStack,
    List,
    Modal,
    Page,
    Text,
} from '@shopify/polaris';
import {useContext, useEffect, useState} from 'react';
import {useNavigate} from 'react-router-dom';
import {toast} from 'react-toastify';
import {AppContext} from '../../context/AppProvider';
import BlockService from '../../services/BlockService';
import {FREE_PLAN_NAME, showToast} from '../../utils/constant';
import NoBlockIcon from '../../utils/NoBlockIcon';
import ProgressBar from '../../utils/ProgressBar';
import AppExtensionBanner from '../Banner/AppExtensionBanner';
import PageViewsLimitCrossedBanner from '../Banner/PageViewsLimitCrossedBanner';
import RateBanner from '../Banner/RateBanner';
import UpgradePlanBanner from '../Banner/UpgradePlanBanner';
import Footer from '../layout/Footer';

const Home = () => {
    const {user, plan, discard_flag, increaseDiscardFlag, icon_blocks, setIconBlocks} = useContext(
        AppContext);
    const [loading, setLoading] = useState(false);
    const [showUpgradePlanBanner, setShowUpgradePlanBanner] = useState(false);
    const navigate = useNavigate();
    const [blocks, setBlocks] = useState(icon_blocks);
    const [deleteBlockId, setDeleteBlockId] = useState('');

    if (discard_flag !== 1) {
        increaseDiscardFlag(1);
    }

    useEffect(() => {
        if (user && user.plan_id == null) {
            navigate('/plan-pricing');
        }
    }, [user]);

    useEffect(() => {
        setBlocks(icon_blocks);
        // getIconBlocks();
    }, [icon_blocks]);

    const getIconBlocks = () => {
        setLoading(true);
        BlockService.getBlocks().then((response) => {
            setBlocks(response.data.data);
        }).catch((error) => {
        }).finally(() => {
            setLoading(false);
        });
    };

    const handleIconBlockStatus = (index, id, status) => {
        const active_blocks_count = blocks.reduce((acc, obj) => {
            if (obj.is_enabled == 1) {
                return acc + 1;
            } else {
                return acc;
            }
        }, 0);

        if (plan?.name == FREE_PLAN_NAME && active_blocks_count >=  1 && status === true) {
            toast("Free Plan Can Have Upto One IconBlock As Active.",{type:'warning'})
            return;
        }
        BlockService.updateBlockStatus(id, status).then((response) => {
            if (response.data.success) {
                let block = {...blocks[index], is_enabled: status};
                let _blocks = [...blocks];
                _blocks[index] = block;
                // setBlocks(_blocks);
                setIconBlocks([..._blocks]);

                showToast('IconBlock Status Changed Successfully:',
                    {type: 'success', autoClose:500 });
            }
        }).catch((error) => {
        }).finally(() => {
            setLoading(false);
        });
    };
    const handleEditBlock = (block_id) => {
        navigate('/add-edit-block', {state: {block_id: block_id}});
    };

    const handleAddIconBlock = () => {
        if (plan?.max_block_limit === null ||
            (plan?.max_block_limit != null && blocks.length <
                plan.max_block_limit)) {
            navigate('/add-edit-block');
        } else {
            setShowUpgradePlanBanner(true);
        }
    };

    const handleDeleteBlock = () => {
        BlockService.deleteBlock(deleteBlockId).then((response) => {
            if (response.data.success) {
                let _blocks = blocks.filter((b) => b.id !== deleteBlockId);
                setDeleteBlockId('');
                // setBlocks(_blocks);
                setIconBlocks([..._blocks]);
                showToast('IconBlock Deleted Successfully', {type: 'success'});
            }
        }).catch((error) => {
        }).finally(() => {
            setLoading(false);
        });
    };

    const handleDuplicateBlock = (index, block_id) => {
        if (plan?.max_block_limit !== null && blocks.length >=
            plan.max_block_limit) {
            setShowUpgradePlanBanner(true);
            return;
        }

        BlockService.duplicate(block_id).then((response) => {
            if (response.data?.data) {
                let block = {...blocks[index]};
                block.id = response.data.data;
                let _blocks = [...blocks];
                _blocks.push(block);
                setIconBlocks([..._blocks]);

                showToast('IconBlock Duplicated Successfully', {type: 'success'});
            }
        }).catch((error) => {
        }).finally(() => {
            setLoading(false);
        });
    };

    return (
        <Page fullWidth={true}>
            <AppExtensionBanner />
            <PageViewsLimitCrossedBanner />
            {
                blocks?.length > 0 ?
                    <RateBanner/>
                    : null
            }

            {
                showUpgradePlanBanner ?
                    <UpgradePlanBanner
                        hideBanner={() => setShowUpgradePlanBanner(false)}/>
                    : null
            }



            <Modal open={deleteBlockId} title={'Delete Block'}
                   onClose={() => setDeleteBlockId('')}>
                <Modal.Section>
                    <Text variant={'bodyLg'}> Do you want to delete icon
                        block? </Text>
                </Modal.Section>
                <Modal.Section>
                    <LegacyStack distribution={'trailing'}
                                 spacing={'extraTight'}>
                        <Button onClick={() => setDeleteBlockId(
                            '')}> Close </Button>
                        <Button onClick={() => handleDeleteBlock()}
                                destructive> Delete </Button>
                    </LegacyStack>
                </Modal.Section>
            </Modal>

            {
                (!loading) ?
                    (blocks.length > 0) ?
                            <>
                                {/*Show only when plan has limit*/}
                                {
                                    plan?.page_views_threshold != null ?
                                        <ProgressBar/>
                                        : null
                                }

                                <Box padding={'5'} id={'block-list-header'}>
                                    <div blockAlign={'baseline'}
                                            align={'space-between'}>
                                        <Text variant={'headingXl'} as={'span'}>Icon
                                            Blocks</Text>
                                        <Button primary
                                                onClick={() => handleAddIconBlock()}>
                                            Add Icon Block
                                        </Button>
                                    </div>
                                </Box>
                                {
                                    blocks.map((block, index) => {
                                        return (
                                            <Box key={index}>
                                                <div key={1}
                                                     className="icon-block-container">
                                                    <Box padding="3"
                                                         paddingInlineStart={'4'}>
                                                        <div className="blockContainer">
                                                            <div
                                                                className={'block-clickable'}
                                                                onClick={() => handleEditBlock(
                                                                    block.id)}>
                                                                <h2 className="heading_title"> {block.name} </h2>
                                                                <div
                                                                    className={'block-icon-titles'}>
                                                                    <List>
                                                                        <div
                                                                            align={'start'}
                                                                            gap="8">
                                                                            {
                                                                                block.icons.map(
                                                                                    (
                                                                                        icon,
                                                                                        index) => {
                                                                                        return (
                                                                                            <List.Item
                                                                                                key={index}>{icon.title}</List.Item>
                                                                                        );
                                                                                    })
                                                                            }
                                                                        </div>
                                                                    </List>
                                                                </div>
                                                            </div>
                                                            <div
                                                                className={'block-action'}>
                                                                <ButtonGroup>
                                                                    <div
                                                                        className="check-button">
                                                                        <label
                                                                            className="switch">
                                                                            <input
                                                                                type="checkbox"
                                                                                name="block_status[]"
                                                                                className="block_status"
                                                                                onChange={() => {}}
                                                                                checked={block.is_enabled}/>
                                                                            <div
                                                                                onClick={() => handleIconBlockStatus(
                                                                                    index,
                                                                                    block.id,
                                                                                    !block.is_enabled)}
                                                                                className="slider round"></div>
                                                                        </label>
                                                                    </div>
                                                                    <Button
                                                                        onClick={() => handleDuplicateBlock(
                                                                            index,
                                                                            block.id)}>
                                                                        Duplicate
                                                                    </Button>
                                                                    <Button
                                                                        onClick={() => setDeleteBlockId(
                                                                            block.id)}
                                                                        destructive>
                                                                        Delete
                                                                    </Button>
                                                                </ButtonGroup>
                                                            </div>
                                                        </div>
                                                    </Box>
                                                </div>

                                            </Box>
                                        );
                                    })
                                }
                            </>
                            : <NoBlockIcon/>
                     : null
            }

            <Footer/>
        </Page>
    );
};

export default Home;
