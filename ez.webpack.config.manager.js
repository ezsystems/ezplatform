const findConfig = (eZConfigs, configName) => {
    const eZConfig = eZConfigs.find((eZConfig) => eZConfig.name === configName);

    if (!eZConfig) {
        throw new Error(`Couldn't find config with name: "${configName}". Please check if there is a typo in the name.`);
    }

    return eZConfig;
};
const findItems = (eZConfigs, configName, entryName) => {
    const eZConfig = findConfig(eZConfigs, configName);
    const items = eZConfig.entry[entryName];

    if (!items) {
        throw new Error(`Couldn't find entry with name: "${entryName}". Please check if there is a typo in the name.`);
    }

    return items;
};
const replace = ({ eZConfigs, configName, entryName, itemToReplace, newItem }) => {
    const items = findItems(eZConfigs, configName, entryName);
    const indexToReplace = items.indexOf(itemToReplace);

    if (indexToReplace < 0) {
        throw new Error(`Couldn't find item "${itemToReplace}" in entry "${entryName}" in config "${configName}". Please check if there is a typo in the name.`);
    }

    items[indexToReplace] = newItem;
};
const remove = ({ eZConfigs, configName, entryName, itemsToRemove }) => {
    const items = findItems(eZConfigs, configName, entryName);
    const eZConfig = findConfig(eZConfigs, configName);

    eZConfig.entry[entryName] = items.filter((item) => !itemsToRemove.includes(item));
};
const add = ({ eZConfigs, configName, entryName, newItems }) => {
    const items = findItems(eZConfigs, configName, entryName);
    const eZConfig = findConfig(eZConfigs, configName);

    eZConfig.entry[entryName] = [...items, ...newItems];
};

module.exports = {
    replace,
    remove,
    add
};
