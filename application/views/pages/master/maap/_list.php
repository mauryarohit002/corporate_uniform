<?php $this->load->view('pages/component/_list', [
                                                    'add' => 'onclick=popup('.json_encode([]).')',
                                                    'download'  => '<li class="breadcrumb-item active text-uppercase" aria-current="download-data">
                                                                        <a 
                                                                            type="button" 
                                                                            class="btn btn-sm btn-primary" 
                                                                            href="'.assets("import/master/maap.xlsx").'" 
                                                                            data-toggle="tooltip" 
                                                                            data-placement="bottom" 
                                                                            title="DOWNLOAD DATA" 
                                                                            download
                                                                        ><i class="text-info fa fa-download"></i></a>
                                                                    </li>',
                                                ]); ?>