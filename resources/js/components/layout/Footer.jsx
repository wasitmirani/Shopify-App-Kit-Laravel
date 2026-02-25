import {Box, Image, Text} from '@shopify/polaris';
import React from 'react';
import {fb_group_url} from '../../utils/constant';

const Footer = () => {
    return (
        <div className={"footer"}>
            <Box borderRadius={3} padding={'5'}>
                <div align={'space-between'}>
                    <div className={"footer-msg"}>
                        <Text variant={'bodyLg'} as={'span'} class={"footer-msg"}>
                            Get daily updates, tips & hacks to help you grow your eCommerce business with advice from other merchants and experts.
                        </Text>
                    </div>
                    <a href={fb_group_url} target="_blank">
                        <Image  alt={"Join Facebook Group"} className={"join-fbgroup-icon"} source={'/images/facebook-button-join-group.png'}></Image>
                    </a>
                </div>
            </Box>
        </div>
    );
}

export  default Footer;
