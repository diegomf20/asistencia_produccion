<template>
    <div>
        <div class="row">
            <!-- <div class="col-sm-4">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Nuevo Proceso</h4>
                    </div>
                    <div class="card-body">
                        <form action="" v-on:submit.prevent="grabarNuevo()">
                            <Input title="Codigo:" v-model="proceso.codigo" :error="errors.codigo"></Input>
                            <Input title="Nombre:" v-model="proceso.nom_proceso" :error="errors.nom_proceso"></Input>
                            <div class="text-center">
                                <button type="submit" class="btn btn-success">Guardar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div> -->
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Centros de Costos</h4>
                        <!-- <button class="btn btn-sm btn-danger" @click="sincronizar()">Sincronizar</button> -->
                    </div>
                    <div class="card-body">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Código</th>
                                    <th>Descripcion</th>
                                    <th>Fundo</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(proceso,index) in table.data">
                                    <td>{{proceso.id}}</td>
                                    <td>{{proceso.nom_proceso}}</td>
                                    <td>

                                        <div v-if="proceso_editar_index!=index">
                                            {{ proceso.fundo_id }}
                                            <button class="btn-link-warning" @click="proceso_editar_index=index">
                                                <i class="material-icons">edit</i>
                                            </button>
                                        </div>
                                        <div v-else>
                                            <select v-model="proceso.fundo_id">
                                                <option :value="null">Todos los Fundos</option>
                                                <option :value="fundo.id" v-for="fundo in fundos">{{ fundo.nom_fundo }}</option>
                                            </select>
                                            <button class="btn-link-info" @click="editar(proceso.id,proceso.fundo_id)"><i class="material-icons">save</i></button>
                                            <button class="btn-link-danger" @click="proceso_editar_index=-1"><i class="material-icons">cancel</i></button>
                                        </div>
                                    </td>
                                    <!-- <td>
                                        <button @click="abrirEditar(proceso.id)" class="btn-link-info">
                                            <i class="material-icons">create</i>
                                        </button>
                                    </td> -->
                                    <td>
                                        <button v-if="proceso.estado=='0'" @click="actualizarEstado(proceso.id)" class="btn-link-info">
                                            <i class="material-icons">radio_button_checked</i>
                                        </button>
                                        <button v-else @click="actualizarEstado(proceso.id)" class="btn-link-gray">
                                            <i class="material-icons">radio_button_unchecked</i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="pagination">
                            <a v-for="n in table.last_page" :class="{active: table.from==n}" @click="listar(n)">{{n}}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--Modal Editar-->
        <!-- <div id="modal-editar" class="modal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Editar proceso</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="" v-on:submit.prevent="grabarEditar()">
                            <Input title="Codigo:" v-model="proceso_editar.codigo" :error="errors_editar.codigo"></Input>
                            <Input title="Nombre:" v-model="proceso_editar.nom_proceso" :error="errors_editar.nom_proceso"></Input>
                            <div class="text-center">
                                <button type="submit" class="btn btn-success">Guardar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div> -->
        <!--Fin Modal Editar-->
    </div>
</template>
<script>
import Input from '../../dragon-desing/dg-input.vue'
export default {
    components:{
        Input
    },
    data() {
        return {
            proceso: this.iniproceso(), //datos de logeo
            proceso_editar: this.iniproceso(),
            errors: {}, //datos de errores
            errors_editar: {}, //datos de errores
            //Datos de Tabla:
            table:{
                data:[]
            },
            url: null,
            fundos: [],
            proceso_editar_index:-1,
        }
    },
    mounted() {
        this.listar();
        this.listarFundos();
    },
    methods: {
        listar(n=this.table.from){
            axios.get(url_base+'/proceso?page='+n)
            .then(response => {
                this.table = response.data;
            })
        },
        listarFundos(){
            axios.get(url_base+'/fundo?all=true')
            .then(response => {
                this.fundos = response.data;
            })
        },
        iniproceso(){
            this.errors={};
            return {
                codigo: null,
                nom_proceso: null,
            }
        },
        grabarNuevo(){
            axios.post(url_base+'/proceso',this.proceso)
            .then(response => {
                var respuesta=response.data;
                switch (respuesta.status) {
                    case "VALIDATION":
                        this.errors=respuesta.data;
                        break;
                    case "OK":
                        this.proceso=this.iniproceso();
                        swal("", "proceso Registrado", "success");
                        this.listar();
                        break;
                    default:
                        break;
                }
            });
        },
        editar(id,fundo_id){
            axios.post(url_base+'/proceso/'+id+'?_method=PUT',{
                fundo_id: fundo_id
            })
            .then(response => {
                var respuesta=response.data;
                switch (respuesta.status) {
                    case "OK":
                        swal("", "Estado Actualizado", "success");
                        this.listar();
                        break;
                    default:
                        break;
                }
                this.proceso_editar_index=-1;
            });
        },
        actualizarEstado(id){
            axios.post(url_base+'/proceso/'+id+'/estado')
            .then(response => {
                var respuesta=response.data;
                switch (respuesta.status) {
                    case "OK":
                        swal("", "Estado Actualizado", "success");
                        this.listar();
                        break;
                    default:
                        break;
                }
            });
        },
        grabarEditar(){
            axios.post(url_base+'/proceso/'+this.proceso_editar.id+'?_method=PUT',this.proceso_editar)
            .then(response => {
                var respuesta=response.data;
                switch (respuesta.status) {
                    case "VALIDATION":
                        this.errors_editar=respuesta.data;
                        break;
                    case "OK":
                        this.proceso_editar=this.iniproceso();
                        this.listar();
                        swal("", "proceso Actualizado", "success");
                        $('#modal-editar').modal('hide');
                        break;
                    default:
                        break;
                }
            });
        },
        abrirEditar(id){
            axios.get(url_base+'/proceso/'+id)
            .then(response => {
                this.proceso_editar = response.data;
            })
            $('#modal-editar').modal();
        },
        // sincronizar(){
        //     axios.get(url_base+'/sincronizar/proceso')
        //     .then(response => {
        //         this.listar();
        //     });
        // },
    },
}
</script>