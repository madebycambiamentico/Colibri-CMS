jquery 3 has modified behavior for:
- toggling visibility (no longer is safe to display/hide) => use css
- .load events are no longer supported. event can be catched with .on(): for example in images use $('<img>').on('load',function(){...})