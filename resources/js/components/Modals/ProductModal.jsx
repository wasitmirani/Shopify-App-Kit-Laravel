import {
    Avatar,
    LegacyFilters,
    Modal,
    ResourceItem,
    ResourceList, Scrollable,
} from '@shopify/polaris';
import $ from 'jquery';
import React, {useCallback, useEffect, useState} from 'react';
import {toast} from 'react-toastify';
import ProductService from '../../services/ProductService';
import {showToast} from '../../utils/constant';

let timeout = 0;
const ProductModal = ({onClose, onSave, selected}) => {
    const [queryValue, setQueryValue] = useState('');
    const [selectedItems, setSelectedItems] = useState(selected);
    const [items, setItems] = useState([]);
    const [loading, setLoading] = useState(false);

    setTimeout(() => {
        $('.custom-resource-list-wrapper  input[type="checkbox"]').prop('disabled',false);
    })
    useEffect(() => {
        if (!timeout) {
            timeout = 300;
        }

        const delayDebounceFn = setTimeout(function() {
            setLoading(true);
            ProductService.getProducts(queryValue).then((response) => {
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

    const handleQueryValueRemove = useCallback(
        () => setQueryValue(''),
        [],
    );
    const handleClearAll = useCallback(() => {
        handleQueryValueRemove();
    }, [handleQueryValueRemove]);

    const resourceName = {
        singular: 'product',
        plural: 'products',
    };

    useEffect(() => {
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
        const {id, image_url, title} = item;
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
            title={'Products'}
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
                            loading={loading}
                            resourceName={resourceName}
                            items={items}
                            renderItem={renderItem}
                            filterControl={filterControl}
                            selectedItems={selectedItems}
                            onSelectionChange={setSelectedItems}
                            selectable

                            emptySearchState={<div>No products available.</div>}
                        />
                    </div>
                </Scrollable>
            </Modal.Section>

        </Modal>
    );
};

export default ProductModal;
