<template>
    <div class="row">
        <div class="col-sm-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Respuesta</h4>
                </div>
                <div class="card-body">
                    <div v-if="alert!=null" :class="'alert alert-'+alert.status" role="alert">
                        {{ alert.data }}
                    </div>
                    <div v-if="respuesta!=null && respuesta.status=='OK'"  class="fotocheck text-center" style="margin-right: auto;margin-left: auto">
                        <img :src="url(respuesta.data.foto)" alt="">
                        <p><b>{{ respuesta.data.nom_operador.split(' ')[0] }} {{ respuesta.data.ape_operador.split(' ')[0] }}</b></p>
                        <hr>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6">
            <!--Modal de pendientes-->
            <div id="modal-pendientes" class="modal" tabindex="-1" role="dialog">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Lista de Pendientes ({{ reporte.length }})</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>DNI</th>
                                        <th>Nombre y Apellidos</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="item in reporte">
                                        <td>{{ item.dni }}</td>
                                        <td>{{ item.nom_operador }} {{ item.ape_operador }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
             <div class="card">
                 <div class="card-header">
                    <h4 class="card-title text-center" v-if="isMovil">TAREO MOVIL <button class="btn btn-danger btn-sm btn-float-right" @click="openPendientes()">P</button></h4>
                    <h4 class="card-title text-center" v-else>TAREO <button class="btn btn-danger btn-sm btn-float-right" @click="openPendientes()">P</button></h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <Input title="Fecha:" v-model="tareo.fecha" type="date"></Input>
                        </div>
                    </div>

                    <Select title="Centro de Costo:" v-model="tareo.proceso_id">
                        <option value="">--SELECCIONAR C.COSTO--</option>
                        <option v-for="proceso in procesos" :value="proceso.id">{{ proceso.id+" - "+ proceso.nom_proceso }}</option>
                    </Select>
                    <Select title="Actividad:" v-model="tareo.area_id">
                        <option value="">--SELECCIONAR ACTIVIDAD--</option>
                        <option v-for="area in areas" :value="area.id">{{ area.nom_area }}</option>
                    </Select>
                    <Select title="Labor:" v-model="tareo.labor_id">
                        <option value="">--SELECCIONAR LABOR--</option>
                        <option v-for="labor in labores" :value="labor.id">{{ labor.nom_labor }}</option>
                    </Select>
                    <form v-on:submit.prevent="guardar()">
                        <Input title="Codigo de Barras" :focusSelect='focus' type="number" v-model="tareo.codigo_barras"></Input>
                        <button type="submit" hidden></button>
                    </form>
                </div>
             </div>
        </div>
    </div>
</template>
<script>
import { mapState,mapMutations } from 'vuex'

import Input from '../../dragon-desing/dg-input.vue'
import Select from '../../dragon-desing/dg-select.vue'

export default {
    components:{
        Input,
        Select
    },
    data() {
        return {
            focus: true,
            isMovil:((navigator.userAgent).search('Android')>-1),
            tareo:{
                fecha: moment().format('YYYY-MM-DD'),
                area_id:null,
                proceso_id:null,
                labor_id:null,
                codigo_barras:null,
            },
            areas:[],
            procesos:[],
            /**Estado de registro */
            respuesta: null,
            alert: null,
            /**Lista de Pendientes */
            reporte:[],
            areasLabor:[]
        }
    },
    mounted() {
        this.listarProcesos();
        this.listarAreasLabor();
    },
    computed: {
        ...mapState(['fundo']),
        labores(){
            for (let i = 0; i < this.areas.length; i++) {
                const area = this.areas[i];
                if (area.id==this.tareo.area_id) {
                    this.tareo.labor_id=null;
                    console.log(area.labores);
                    return area.labores;
                }
            }
            return [];
        }
    },
    methods: {
        url(foto){
            return url_base+'/../storage/operador/'+foto;
        },
        listarAreasLabor(){
            axios.get(url_base+'/area/labor')
            .then(response => {
                this.areas = response.data;
            });
        },
        listarProcesos(){
            axios.get(url_base+'/proceso?all=true&fundo_id='+this.fundo)
            .then(response => {
                this.procesos = response.data;
            });
        },
        clearAlert(){
            setTimeout(() => {
                this.alert=null;
            }, 1000);
        },
        guardar(){
            this.$nextTick(() =>{
                if (((null==this.tareo.codigo_barras) ? '' : this.tareo.codigo_barras ).length==8) {
                    axios.post(url_base+'/tareo?fundo_id='+this.fundo,this.tareo)
                    .then(response => {
                        var respuesta=response.data;
                        console.log(respuesta);
                        
                        switch (respuesta.status) {
                            case "ERROR":
                                this.alert=respuesta;
                                this.respuesta=null;
                                this.clearAlert();
                                break;
                            case "WARNING":
                                if((navigator.userAgent).search('Android')>-1){
                                    swal("", respuesta.data, 'warning');
                                }
                                this.tareo.codigo_barras=null;
                                this.alert={
                                    status: 'warning',
                                    data: respuesta.data
                                };
                                break;
                            case "OK":
                                // swal("", respuesta.data.nom_operador+" "+ respuesta.data.ape_operador, 'warning');
                                this.alert={
                                    status: 'primary',
                                    data: "TAREO: "+respuesta.data.nom_operador+" "+ respuesta.data.ape_operador
                                };
                                // this.respuesta=response.data;
                                this.tareo.codigo_barras=null;
                                break;
                            default:
                                break;
                        }
                    })
                }else{
                    this.tareo.codigo_barras=null;
                    this.respuesta={
                        status: 'ERROR',
                        data: 'Código no Valido'
                    }
                    this.clearAlert();
                }
            })
        },
        openPendientes(){
            axios.get(url_base+'/reporte-pendientes?fecha='+this.tareo.fecha+'&fundo_id='+this.fundo)
            .then(response => {
                this.reporte = response.data;
            })
            $('#modal-pendientes').modal();
        }
    },
}
</script>