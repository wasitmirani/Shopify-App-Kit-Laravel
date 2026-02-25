import React, { useState } from "react";
import {
    Button,
    Popover,
    LegacyStack,
} from "@shopify/polaris";
import { ChromePicker } from "react-color";

const ColorPickerPopover = props => {
    const [showColorPicker, setShowColorPicker] = useState(false);

    return (
        <>
            <LegacyStack alignment="center">
                <Popover
                    active={showColorPicker}
                    activator={
                        <div className={"btn-wrapper"}>
                            {
                                <Button
                                    fullWidth
                                    plain
                                    onClick={() => setShowColorPicker(!showColorPicker)}
                                >
                                    <div
                                        id="btnDiv"
                                        style={{
                                            height: "2rem",
                                            width: "2rem",
                                            borderRadius:'50%',
                                            backgroundColor: props.value,
                                            padding: "0px",
                                            border: "1px solid black"
                                        }}
                                    />
                                </Button>
                            }
                        </div>
                    }
                    onClose={() => setShowColorPicker(false)}
                >
                    <Popover.Section>
                        <ChromePicker
                            color={props.value}
                            onChange={props.onChange}
                            disableAlpha={true}
                        />
                    </Popover.Section>
                </Popover>
            </LegacyStack>
        </>
    );
};

export default ColorPickerPopover;
