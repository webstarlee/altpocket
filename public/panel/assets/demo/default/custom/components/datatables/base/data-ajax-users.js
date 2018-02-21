//== Class definition

var DatatableRemoteAjaxDemo = function() {
  //== Private functions

  // basic demo
  var demo = function() {

    var datatable = $('.m_datatable').mDatatable({
      // datasource definition
      data: {
        type: 'remote',
        source: {
          read: {
            // sample GET method
            method: 'GET',
            url: '/admin/users/get',
            map: function(raw) {
              // sample data mapping
              var dataSet = raw;
              if (typeof raw.data !== 'undefined') {
                dataSet = raw.data;
              }
              return dataSet;
            },
          },
        },
        pageSize: 10,
        saveState: {
          cookie: false,
          webstorage: false,
        },
        serverPaging: true,
        serverFiltering: true,
        serverSorting: true,
      },

      // layout definition
      layout: {
        theme: 'default', // datatable theme
        class: '', // custom wrapper class
        scroll: false, // enable/disable datatable scroll both horizontal and vertical when needed.
        footer: false // display/hide footer
      },

      // column sorting
      sortable: true,

      pagination: true,

      toolbar: {
        // toolbar items
        items: {
          // pagination
          pagination: {
            // page size select
            pageSizeSelect: [10, 20, 30, 50, 100],
          },
        },
      },

      search: {
        input: $('#generalSearch'),
      },

      // columns definition
      columns: [
        {
          field: 'id',
          title: '#',
          width: 40,
          selector: false,
          textAlign: 'center',
        }, {
          field: 'username',
          title: 'Username',
          width: 150,
          template: function(row) {

            if(row.display_role !== "")
            {
              var color = row.display_role.color;
              var style = row.display_role.style;
              if(row.display_role.emblem !== "")
              {
                var emblem = "<img src='/awards/"+row.display_role.emblem+"' style='width:16px!important;margin-right:5px;'>";
              } else {
                var emblem = "";
              }
            } else {
              var color = "";
              var style = "";
              var emblem = ""
            }

            if(row.avatar !== "default.jpg")
            {
              return '<span style="color:'+ color + ';' + style + '"><img style="width:24px;border-radius:100%;margin-right:5px;" src="/uploads/avatars/' + row.id + "/" + row.avatar + '"/>' + emblem + row.username + "</span>";
            } else {
              return '<img style="width:24px;border-radius:100%;margin-right:5px;" src="https://altpocket.io/assets/img/default.png">' + row.username;
            }
          }
          // sortable: 'asc', // default sort
        }, {
          field: 'primary_role',
          title: 'Primary Role',
          width: 150,
          template: function(row) {
            if(row.display_role !== "") {
              if(row.display_role.emblem !== "")
              {
                var emblem = "<img src='/awards/"+row.display_role.emblem+"' style='width:16px!important;margin-right:5px;'>";
              } else {
                var emblem = "";
              }

              return "<span style='color:"+row.display_role.color+";"+row.display_role.style+"'>" + emblem + row.display_role.title + "</span>";
            } else {
              return "";
            }
          }
          // sortable: 'asc', // default sort
        }, {
          field: 'Actions',
          width: 110,
          title: 'Actions',
          sortable: false,
          overflow: 'visible',
          template: function(row) {
            return '\
						<a href="/admin/users/edit/'+row.username+'" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill" title="Edit details">\
							<i class="la la-edit"></i>\
						</a>\
					';
          },
        }],
    });

    var query = datatable.getDataSourceQuery();

    $('#m_form_status').on('change', function() {
      // shortcode to datatable.getDataSourceParam('query');
      var query = datatable.getDataSourceQuery();
      query.Status = $(this).val().toLowerCase();
      // shortcode to datatable.setDataSourceParam('query', query);
      datatable.setDataSourceQuery(query);
      datatable.load();

    }).val(typeof query.Status !== 'undefined' ? query.Status : '');

    $('#m_form_type').on('change', function() {
      // shortcode to datatable.getDataSourceParam('query');
      var query = datatable.getDataSourceQuery();
      query.Type = $(this).val().toLowerCase();
      // shortcode to datatable.setDataSourceParam('query', query);
      datatable.setDataSourceQuery(query);
      datatable.load();

    }).val(typeof query.Type !== 'undefined' ? query.Type : '');

    $('#m_form_status, #m_form_type').selectpicker();

  };

  return {
    // public functions
    init: function() {
      demo();

    },
  };
}();

jQuery(document).ready(function() {
  DatatableRemoteAjaxDemo.init();
});
