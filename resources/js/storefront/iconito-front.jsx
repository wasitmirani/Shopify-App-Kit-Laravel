import $ from 'jquery'
import { useState} from 'react';
import Cart from './Cart';
import Index from './Index';
import Manual from './Manual';
import Product from './Product';
import  ReactDOM from "react-dom"
import "../../css/front.css"

let mountNode = document.createElement('div');
mountNode.id="iconito_inject_script";
document.body.appendChild(mountNode);

const Iconito = () => {
    const [data,setData] = useState({
        pageType: $("#iconito-page-type").val(),
        product_id: $("#iconito-product-id").val(),
        shop_domain: $("#iconito-shop-permanent-domain").val(),
        collection_ids: $("#iconito-collection-ids").val() ?? '',
        product_tags: $("#iconito-product-tags").val() ?? ''
    });
    window.iconito_host = data.shop_domain;

    const renderPage = () =>{
        switch (data.pageType){
            case 'index':
                return <Index data={data} />;

            case 'product':
                return <Product data={data} />;

            case 'cart':
                return <Cart data={data} />;

            default:
                return <Manual data={data} />
        }
    }
    return (
        <>
            {
                renderPage()
            }
        </>
    )
}

ReactDOM.render(<Iconito />,mountNode);

