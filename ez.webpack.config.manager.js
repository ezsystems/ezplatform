const fs = require('fs');
const findItems = (eZConfig, entryName) => {
    const items = eZConfig.entry[entryName];

    if (!items) {
        throw new Error(`Couldn't find entry with name: "${entryName}". Please check if there is a typo in the name.`);
    }

    return items;
};
const replace = ({ eZConfig, entryName, itemToReplace, newItem }) => {
    const items = findItems(eZConfig, entryName);
    const indexToReplace = items.indexOf(fs.realpathSync(itemToReplace));

    if (indexToReplace < 0) {
        throw new Error(`Couldn't find item "${itemToReplace}" in entry "${entryName}". Please check if there is a typo in the name.`);
    }

    items[indexToReplace] = newItem;
};
const remove = ({ eZConfig, entryName, itemsToRemove }) => {
    const items = findItems(eZConfig, entryName);
    const realPathItemsToRemove = itemsToRemove.map((item) => fs.realpathSync(item));

    eZConfig.entry[entryName] = items.filter((item) => !realPathItemsToRemove.includes(item));
};
const add = ({ eZConfig, entryName, newItems }) => {
    const items = findItems(eZConfig, entryName);

    eZConfig.entry[entryName] = [...items, ...newItems];
};

module.exports = {
    replace,
    remove,
    add
};
