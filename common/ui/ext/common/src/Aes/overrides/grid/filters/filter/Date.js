/**
 * Allow configurable date format when creating a store filter.
 *
 * To set a global default app-wise, include this fix and use something like:
 *
 *   Ext.grid.filters.filter.Date.prototype.dateWriteFormat = 'c';
 *
 */
Ext.define('Aes.overrides.grid.filters.filter.Date', {
  override: 'Ext.grid.filters.filter.Date',
 
  config: {
    /**
     * @cfg {String} dateWriteFormat
     *
     * The date format to use when creating store filter. For available formats see {@link Ext.Date.format}.
     * Defaults to 'timestamp'
     */
    dateWriteFormat: null
  },
 
  convertValue: function (value, convertToDate) {
    var dateFormat;
 
    if (convertToDate && !Ext.isDate(value)) {
      value = Ext.isDate(value);
    } else if (!convertToDate && Ext.isDate(value)) {
      dateFormat = this.getDateWriteFormat();
      if (dateFormat) {
        value = Ext.Date.format(value, dateFormat);
      } else {
        value = (+value);
      }
    }

    return value;
  }
});