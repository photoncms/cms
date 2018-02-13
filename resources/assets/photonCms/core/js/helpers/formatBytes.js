/**
 * Formats byte to human readable value
 *
 * @param   {integer}  bytes
 * @return  {string}
 */
export const formatBytes = (bytes) => {
    if(bytes == 0) {
        return '0 Bytes';
    }

    let k = 1024;

    const decimals = 2;

    const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];

    let i = Math.floor(Math.log(bytes) / Math.log(k));

    return parseFloat((bytes / Math.pow(k, i)).toFixed(decimals)) + ' ' + sizes[i];
};
