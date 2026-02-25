import {
    Avatar,
    LegacyFilters,
    Modal,
    ResourceItem,
    ResourceList, Scrollable,
} from '@shopify/polaris';
import React, {useCallback, useEffect, useState} from 'react';
import {toast} from 'react-toastify';
import CollectionService from '../../services/CollectionService';
import {showToast} from '../../utils/constant';
import $ from "jquery"

let timeout = 0;
const CollectionModal = ({onClose, onSave, selected}) => {
    const [queryValue, setQueryValue] = useState(undefined);
    const [selectedItems, setSelectedItems] = useState(selected);
    const [items, setItems] = useState([]);
    const [loading, setLoading] = useState(false);

    const handleQueryValueRemove = useCallback(
        () => setQueryValue(undefined),
        [],
    );
    const handleClearAll = useCallback(() => {
        handleQueryValueRemove();
    }, [handleQueryValueRemove]);

    const resourceName = {
        singular: 'collection',
        plural: 'collections',
    };


    setTimeout(() => {
        $('.custom-resource-list-wrapper  input[type="checkbox"]').prop('disabled',false);
    })
    useEffect(() => {
        if (!timeout) {
            timeout = 300;
        }

        const delayDebounceFn = setTimeout(function() {
            setLoading(true);
            CollectionService.getCollections(queryValue).then((response) => {
                if (response?.data?.data) {
                    setItems(response.data.data);
                }
            }).catch((error) => {
                showToast(error.response.data.message, {type: 'error'});
            }).finally(() => {
                setLoading(false);
            });
        }, timeout);
        return () => clearTimeout(delayDebounceFn);
    }, [queryValue]);

    const filterControl = (
        <LegacyFilters
            disableQueryField={loading}
            queryValue={queryValue}
            filters={[]}
            onQueryChange={setQueryValue}
            onQueryClear={handleQueryValueRemove}
            onClearAll={handleClearAll}
        >
        </LegacyFilters>
    );

    function renderItem(item) {
        const {id, title, image_url} = item;
        const media = <Avatar
            source={image_url ?? '/images/no-image.png'}
            product shape={'square'} size="medium" name={title}/>;

        return (
            <ResourceItem id={id} media={media}>
                {title}
            </ResourceItem>
        );
    }

    return (
        <Modal
            open={true}
            title={'Collections'}
            onClose={() => onClose()}
            primaryAction={{
                content: 'Save',
                onAction: () => onSave(selectedItems),
            }}
            secondaryActions={[
                {
                    content: 'Close',
                    onAction: () => onClose(),
                },
            ]}
        >
            <Modal.Section>
                <Scrollable horizontal={false} shadow
                            style={{height: '500px'}}>
                <div className={"custom-resource-list-wrapper"}>
                    <ResourceList
                        resourceName={resourceName}
                        items={items}
                        renderItem={renderItem}
                        filterControl={filterControl}
                        selectedItems={selectedItems}
                        onSelectionChange={setSelectedItems}
                        selectable
                        loading={loading}
                        emptySearchState={<div>No collections available.</div>}
                    />
                </div>
                </Scrollable>
            </Modal.Section>
        </Modal>
    );
};

export default CollectionModal;
