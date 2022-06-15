import {Controller} from "@hotwired/stimulus";

// HTML dataable controller, works with DataTableComponent, which generates an HTML table.
// see api_datatables_controller for remote data loading use API Platform

// import $ from 'jquery'; // for datatables.
// // import {SurvosDataTable} from 'survos-datatables';

import {default as axios} from "axios";

// require('./js/Components/DataTables');
const DataTable = require('datatables.net');
import ('datatables.net-bs5');
import('datatables.net-select-bs5');
// import('datatables.net-buttons-bs5');

import 'datatables.net-scroller';
import 'datatables.net-scroller-bs5';
// import 'datatables.net-searchpanes-bs5'
import 'datatables.net-fixedheader-bs5';

// import {Modal} from "bootstrap"; !!
// https://stackoverflow.com/questions/68084742/dropdown-doesnt-work-after-modal-of-bootstrap-imported
import Modal from 'bootstrap/js/dist/modal';
// import cb from "../js/app-buttons";


export default class extends Controller {
    static targets = ['table', 'modal', 'modalBody', 'fieldSearch', 'message'];
    static values = {
        sortableFields: {type: String, default: '{}'},
        filter: {type: String, default: ''}
    }

    connect() {
        super.connect(); //
        // this.sortableFields = JSON.parse(this.sortableFieldsValue||'[]');
        // this.filter = JSON.parse(this.filterValue||'[]')
        // console.log('hi from ' + this.identifier, this.sortableFields, this.filter);
        //
        // console.log(this.hasTableTarget ? 'table target exists' : 'missing table target')
        // console.log(this.hasModalTarget ? 'target exists' : 'missing modalstarget')
        // // console.log(this.fieldSearch ? 'target exists' : 'missing fieldSearch')
        // console.log(this.sortableFieldsValue);
        // console.assert(this.hasModalTarget, "Missing modal target");
        this.that = this;

        this.tableElement = false;
        if (this.hasTableTarget) {
            this.tableElement = this.tableTarget;
        } else if (this.element.tagName === 'TABLE') {
            this.tableElement = this.element;
        } else {
            this.tableElement = document.getElementsByTagName('table')[0];
        }
        // else {
        //     console.error('A table element is required.');
        // }
        this.dt = this.initDataTable(this.tableElement);

    }

    openModal(e) {
        console.error('yay, open modal!', e, e.currentTarget, e.currentTarget.dataset);

        this.modalTarget.addEventListener('show.bs.modal',  (e) => {
            console.log(e, e.relatedTarget, e.currentTarget);
            // do something...
        });

        this.modal = new Modal(this.modalTarget);
        console.log(this.modal);
        this.modal.show();

    }

    createdRow( row, data, dataIndex )
    {
        // we could add the thumbnail URL here.
        // console.log(row, data, dataIndex, this.identifier);
        // let aaController = 'projects';
        // row.classList.add("text-danger");
        // row.setAttribute('data-action', aaController + '#openModal');
        // row.setAttribute('data-controller', 'modal-form', {formUrl: 'test'});
    }

    notify(message) {
        console.log(message);
        this.messageTarget.innerHTML = message;
    }



    handleTrans(el)
    {
        console.log(el);
        let transitionButtons = el.querySelectorAll('button.transition');
        // console.log(transitionButtons);
        transitionButtons.forEach( btn => btn.addEventListener('click', (event) => {
            const isButton = event.target.nodeName === 'BUTTON';
            if (!isButton) {
                return;
            }
            console.log(event, event.target, event.currentTarget);

            let row  = this.dt.row( event.target.closest('tr') );
            let  data = row.data();
            console.log(row, data);
            this.notify('deleting ' + data.id);

            // console.dir(event.target.id);
        }));

    }

    requestTransition(route, entityClass, id) {

    }

    addButtonClickListener(dt)
    {
        console.log("Listening for transition events");

        dt.on('click', 'tr td button.transition',  ($event) => {
            console.log($event.currentTarget);
            let target = $event.currentTarget;
            var data = dt.row( target.closest('tr') ).data();
            let transition = target.dataset['t'];
            console.log(transition, target);
            console.log(data, $event);
            this.that.modalBodyTarget.innerHTML = transition;
            this.modal = new Modal(this.modalTarget);
            this.modal.show();

        });

        dt.on('click', 'tr td button .modal',  ($event, x) => {
            console.log($event, $event.currentTarget);
            var data = dt.row( $event.currentTarget.closest('tr') ).data();
            console.log(data, $event, x);

            let btn = $event.currentTarget;
            let modalRoute = btn.dataset.modalRoute;
            if (modalRoute) {
                this.modalBodyTarget.innerHTML = data.code;
                this.modal = new Modal(this.modalTarget);
                this.modal.show();
                console.assert(data.uniqueIdentifiers, "missing uniqueIdentifiers, add @Groups to entity")
                let formUrl = Routing.generate(modalRoute, {...data.uniqueIdentifiers, _page_content_only: 1});

                axios({
                    method: 'get', //you can set what request you want to be
                    url: formUrl,
                    // data: {id: varID},
                    // headers: {
                    //     _page_content_only: '1' // could send blocks that we want??
                    // }
                })
                    .then( response => this.modalBodyTarget.innerHTML = response.data)
                    .catch( error => this.modalBodyTarget.innerHTML = error)
                ;
            }

        });
    }
    addRowClickListener(dt)
    {
        dt.on('click', 'tr td',  ($event) => {
            let el = $event.currentTarget;
            console.log($event, $event.currentTarget);
            var data = dt.row( $event.currentTarget ).data();
            var btn = el.querySelector('button');
            console.log(btn);
            let modalRoute = null;
            if (btn) {
                console.error(btn, btn.dataset, btn.dataset.modalRoute);
                modalRoute = btn.dataset.modalRoute;
            }


            console.error(el.dataset, data, $event.currentTarget, );
            console.log(this.identifier + ' received an tr->click event', data, el);

            if(el.querySelector("a")) {
                return; // skip links, let it bubble up to handle
            }

            if (modalRoute) {
                this.modalBodyTarget.innerHTML = data.code;
                this.modal = new Modal(this.modalTarget);
                this.modal.show();
                console.assert(data.uniqueIdentifiers, "missing uniqueIdentifiers, add @Groups to entity")
                let formUrl = Routing.generate(modalRoute, data.uniqueIdentifiers);

                axios({
                    method: 'get', //you can set what request you want to be
                    url: formUrl,
                    // data: {id: varID},
                    headers: {
                        _page_content_only: '1' // could send blocks that we want??
                    }
                })
                    .then( response => this.modalBodyTarget.innerHTML = response.data)
                    .catch( error => this.modalBodyTarget.innerHTML = error)
                ;
            }
        } );
    }

    initDataTable(el)
    {
        console.log('init table ', el);
        // let dt = $(el).DataTable({
        let dt = new DataTable(el, {
            createdRow: this.createdRow,
            // paging: true,
            scrollY: '70vh', // vh is percentage of viewport height, https://css-tricks.com/fun-viewport-units/
            // scrollY: true,
            displayLength: 50, // not sure how to adjust the 'length' sent to the server
            // pageLength: 15,
            columnDefs: this.columnDefs,
            orderCellsTop: true,
            fixedHeader: true,

            deferRender:    true,
            // scrollX:        true,
            scrollCollapse: true,
            scroller: {
                // rowHeight: 90, // @WARNING: Problematic!!
                displayBuffer: 10,
                loadingIndicator: true,
            },
            dom: '<"js-dt-buttons"B><"js-dt-info"i>ft',
            buttons: [], // this.buttons,
        });


        return dt;

    }

}
