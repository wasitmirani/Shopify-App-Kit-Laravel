import { toast } from 'react-toastify';

export const fontOptions = [
    { label: "Roboto", value: "Roboto" },
    { label: "Poppins", value: "Poppins" },
    { label: "Pushster", value: "Pushster" },
    { label: "Liquorice", value: "Liquorice" },
    { label: "Open Sans", value: "Open Sans" },
    { label: "Vujahday Script", value: "Vujahday Script" },
    { label: "Lato", value: "Lato" },
    { label: "Shizuru", value: "Shizuru" },
    { label: "Montserrat", value: "Montserrat" },
    { label: "Oswald", value: "Oswald" },
    { label: "Roboto Mono", value: "Roboto Mono" },
    { label: "Raleway", value: "Raleway" },
    { label: "Playfair Display", value: "Playfair Display" },
    { label: "Inter", value: "Inter" },
    { label: "Rubik", value: "Rubik" }
];

export const fontWeightOptions = [
    { label: "100", value: "100" },
    { label: "200", value: "200" },
    { label: "300", value: "300" },
    { label: "400", value: "400" },
    { label: "500", value: "500" },
    { label: "600", value: "600" },
    { label: "700", value: "700" },
    { label: "800", value: "800" },
    { label: "900", value: "900" },
    { label: "Bold", value: "bold" },
    { label: "Bolder", value: "bolder" },
    { label: "Normal", value: "normal" },
    { label: "Lighter", value: "lighter" },
];

export const alignmentOptions = [
    { label: "Left", value: "left" },
    { label: "Center", value: "center" },
    { label: "Right", value: "right" }
];

export const fontStyleOptions = [
    { label: "Light", value: "light" },
    { label: "Light Italic", value: "light-italic" },
    { label: "Regular", value: "regular" },
    { label: "Italic", value: "regular-italic" },
    { label: "Bold", value: "bold" },
    { label: "Bold Italic", value: "bold-italic" }
];

export const getStyle = (selected_style) => {
    let css_styles = [];

    css_styles['light'] = { "font-style" : '"normal"',"font-weight" : '"300"'};
    // css_styles['light'] = { "font-style" : 'normal',"font-weight" : '300'};
    css_styles['light-italic'] ={'font-style': "italic","font-weight": '300'};
    css_styles['regular'] = {'font-style': 'normal','font-weight': 'normal'};
    css_styles['regular-italic'] = {'font-style': 'italic','font-weight': 'normal'};
    css_styles['bold'] = {'font-style': 'normal', 'font-weight': 'bold'};
    css_styles['bold-italic'] = {'font-style': 'italic', 'font-weight': 'bold'};

    return css_styles[selected_style];
}
export const getNextRegularIconCategory = (category) => {
    let flag =0;
    let categories = {
        ecommerce: 'Ecommerce',
        payment_methods: 'Payment methods',
        social_media: 'Social media',
        ecology: 'Ecology',
        diet_nutrition: 'Diet nutrition',
        pets: 'Pets',
        printing: 'Printing',
        emojis_colors: 'Emojis colors',
        recycling: 'Recycling',
        medicine: 'Medicine',
        beauty: 'Beauty',
        coffee: 'Coffee',
        mechanic: 'Mechanic',
        various: 'Various'
    };
    let next_cat =''
    Object.entries(categories).map((cat,i) => {
        if(flag ==1 && next_cat ==''){
            next_cat = cat;
        }
        if(cat[1] == category){
            flag = 1
        }
    })

    return next_cat;
}


export const getNext3DIconCategory = (category) => {
    let flag =0;
    let categories = {
        brands_and_social_medias: 'Brands and social medias',
        business_and_finance: 'Business and finance',
        customer_support: 'Customer support',
        ecology_and_recycling: 'Ecology and recycling',
        ecommerce_and_shipping: 'Ecommerce and shipping',
        fruit_and_vegetable: 'Fruit and vegetable',
        medicine: 'Medicine',

    };
    let next_cat =''
    Object.entries(categories).map((cat,i) => {
        if(flag ==1 && next_cat ==''){
            next_cat = cat;
        }
        if(cat[1] == category){
            flag = 1
        }
    })

    return next_cat;
}

export const getCategoryByIndex = (index) =>{
    let categories = {
        ecommerce: 'Ecommerce',
        payment_methods: 'Payment methods',
        social_media: 'Social media',
        ecology: 'Ecology',
        diet_nutrition: 'Diet nutrition',
        pets: 'Pets',
        printing: 'Printing',
        emojis_colors: 'Emojis colors',
        recycling: 'Recycling',
        medicine: 'Medicine',
        beauty: 'Beauty',
        coffee: 'Coffee',
        mechanic: 'Mechanic',
        various: 'Various',
        brands_and_social_medias: 'Brands and social medias',
        business_and_finance: 'Business and finance',
        customer_support: 'Customer support',
        ecology_and_recycling: 'Ecology and recycling',
        ecommerce_and_shipping: 'Ecommerce and shipping',
        fruit_and_vegetable: 'Fruit and vegetable'
    };

    return categories[index];
}


export function uniqid(prefix = "", random = false) {
    const sec = Date.now() * 1000 + Math.random() * 1000;
    const id = sec.toString(16).replace(/\./g, "").padEnd(14, "0");

    return `${prefix}${id}${random ? `.${Math.trunc(Math.random() * 100000000)}`:""}`;
};

export const fb_group_url = "http://www.facebook.com/groups/royalecom";
export const FREE_PLAN_NAME = "STARTER";

export const showToast = (message, options) => {
    toast.dismiss(); // Dismiss the current toast, if any
    toast.info(message, options); // Show the new toast
};

export const isStorefrontRequest = () => {
    let storefront = 1;
    if(window.location?.ancestorOrigins?.length > 0 && Object.values(window.location?.ancestorOrigins)?.includes('https://admin.shopify.com')){
        storefront =0;
    }
    return storefront;
}
