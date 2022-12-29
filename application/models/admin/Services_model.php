<?php
	class Services_model extends CI_Model{
        

		public function get_services(){
            $this->db->order_by('created_at DESC');
            return $this->db->get('services')->result();
		}

        //add
        public function add($data)
        {
            return $this->db->insert('services', $data);
        }

        //get
        public function get($id)
        {
            $this->db->where('id', clean_number($id));
            return $this->db->get('services')->row();
        }

        //get
        public function get_by_slug($slug)
        {
            $this->db->where('slug', $slug);
            return $this->db->get('services')->row();
        }

        //update pages
        public function update($id, $data)
        {
            $id = clean_number($id);

            $this->db->where('id', $id);
            return $this->db->update('services', $data);
        }

        //delete 
        public function delete($id)
        {
            $id = clean_number($id);
            $page = $this->get($id);
            if (!empty($page)) {
                $this->db->where('id', $id);
                return $this->db->delete('services');
            }
            return false;
        }
        


        function make_query($category)
        {
            $query = "
            SELECT * FROM services 
            ";

            if(isset($category))
            {
                $category_filter = implode("','", $category);
                $query .= "
                 WHERE category_id IN('".$category_filter."')
                ";
            }
            return $query;
        }

        function count_all($category)
        {
            $query = $this->make_query($category);
            $data = $this->db->query($query);
            return $data->num_rows();
        } 
        


        function fetch_data($limit, $start, $category)
        {
            
            $ci =& get_instance();
            $ci->load->model('admin/categories_model');

            //$ci->users->MyUserFunction();
            
            $query = $this->make_query($category);

            $query .= ' LIMIT '.$start.', ' . $limit;

            $data = $this->db->query($query);

            $output = '';
            if($data->num_rows() > 0)
            {
                foreach($data->result_array() as $row)
                {
                    $category_title = $ci->categories_model->get($row['category_id']);
                    $ct_name = $category_title->name;
                    
                    /*$output .= '
                        <div class="col-xxl-6 col-xl-6 col-lg-6 col-md-6">
                           <div class="product__item white-bg mb-5 text-center">
                              <div class="product__thumb">
                                 <div class="product__thumb-inner fix w-img">
                                    <a href="'.base_url().'service/'. sanitizer($row['slug']) .'">
                                    <!--<img src="'.base_url().'public/uploads/services/'. sanitizer($row['imagelocation']) .'" alt="">-->
                                    <img src="https://www.techinuts.com/public/uploads/services/dea9d780060d8428098572a2f2732b79.jpg" class="img-thumbnail" alt="">
                                    </a>
                                 </div>
                              </div>
                              <div class="product__content">
                                 <div class="product__meta mb-4 d-flex justify-content-between align-items-center">
                                    <div class="product__tag" style="width:100%;text-align:center">
                                       <a>  
                                        ' . sanitizer($ct_name) . '
                                        </a>
                                    </div>
                                    
                                 </div>
                                 <h3 class="product__title mb-2">
                                    <a href="'.base_url().'service/'. sanitizer($row['slug']) .'">'. sanitizer($row['title']) .'</a>
                                 </h3>
                                  <br />
								   <div class="row">		
											
											<div class="col-12 col-md-6">
												<div class="product__price">
												   <!--<span>' . price_currency_format(sanitizer($row['price']), $this->payment_settings->default_currency) .'</span>-->
												   <button class="btn border-danger" style="border-radius:50%">' . price_currency_format(sanitizer($row['price']), $this->payment_settings->default_currency) .'</button>
											   </div>
											</div>
											<div class="col-12 col-md-6">
												<!--<span class="time_tag">'. sanitizer($row['time_duration']) .'</span>-->
												<button class="btn border-warning" style="width:100%">'. sanitizer($row['time_duration']) .'</button>
											</div>
								   </div>
								   
								  <hr>
                                <div class="add-cart">
                                     ' . form_open(base_url('save/cart') ) .'
                                        <input type="hidden" class="buyfield" name="service_id" value="'. sanitizer($row['id']) .'"/>
                                        <input type="hidden" class="buyfield"  min="1" max="10000"
                                        style=" width: 25%; padding: 0.375rem 0.75rem; font-size: 1rem; line-height: 1.5; color: #495057; background-color: #fff; background-clip: padding-box; border: 1px solid #ced4da; border-radius: 0.25rem; transition: border-color .15s ease-in-out,box-shadow .15s ease-in-out; "
                                        name="qty" value="1" placeholder="Quanty"/>
                                        <div class="row">
										
										<div class="col-12 col-md-6">
											<div class="input-group">
											
											<input type="submit" style="width:100%"  class="form-control border-right-0 border py-2 btn btn-primary text-white align-items-center ml-1" name="submit" value="' .get_phrase("add_to_cart") .'"/>
											<span class="input-group-append">
												<div class="input-group-text text-white" style="background-color:#319795;"><i class="las la-shopping-cart"></i></div>
											</span>
											</div><br />
										</div>
										<div class="col-6 col-md-2 ">
										<a href="#" class="btn btn-light border-primary"><i class="las la la-heart-o newtext"></i></a>
										</div>&nbsp;&nbsp;
										<div class="col-6 col-md-2">
										<a href="#" class="btn btn-light border-primary"><i class="las la la-eye newtext"></i></a>
										</div>
										
										</div>
									' . form_close() . '				
                                </div>
                                
                              </div>
                           </div>
                        </div><!--/ col-lg-6 -->  
                    ';*/
					$rowprice = $row['price'] + 5;
					$output .= '
						<div class="col">
								<div class="card rounded-0 product-card">
									<div class="card-header bg-transparent border-bottom-0">
										<div class="d-flex align-items-center justify-content-end gap-3">
											<a href="javascript:;">
												<div class="product-compare"><span><i class="bx bx-git-compare"></i> Compare</span>
												</div>
											</a>
											<a href="javascript:;">
												<div class="product-wishlist"> <i class="bx bx-heart"></i>
												</div>
											</a>
										</div>
									</div>
									<a href="product-details.html">
										<!--<img src="assets/images/products/01.png" class="card-img-top" alt="...">-->
										<img src="https://www.techinuts.com/public/uploads/services/dea9d780060d8428098572a2f2732b79.jpg" class="card-img-top" alt="...">
                                    
									</a>
									<div class="card-body">
										<div class="product-info">
											<a href="javascript:;">
												<p class="product-catergory font-13 mb-1">'.sanitizer($ct_name).'</p>
											</a>
											<a href="javascript:;">
												<h6 class="product-name mb-2">'.sanitizer($row['title']).'</h6>
											</a>
											<div class="d-flex align-items-center">
												<div class="mb-1 product-price"><span class="me-1 text-decoration-line-through">'.price_currency_format(sanitizer($rowprice), $this->payment_settings->default_currency).'</span>
													<span class="fs-5">'.price_currency_format(sanitizer($row['price']), $this->payment_settings->default_currency).'</span>
												</div>
												<div class="cursor-pointer ms-auto">
													<i class="bx bxs-star text-warning"></i>
													<i class="bx bxs-star text-warning"></i>
													<i class="bx bxs-star text-warning"></i>
													<i class="bx bxs-star text-warning"></i>
													<i class="bx bxs-star text-warning"></i>
												</div>
											</div>
											<div class="product-action mt-2">
												<div class="d-grid gap-2">
													<a href="javascript:;" class="btn btn-dark btn-ecomm">	<i class="bx bxs-cart-add"></i>Add to Cart</a>
													<a href="javascript:;" class="btn btn-light btn-ecomm" data-bs-toggle="modal" data-bs-target="#QuickViewProduct"><i class="bx bx-zoom-in"></i>Quick View</a>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
					';
                }
            }
            else
            {
                $output = '<h3>' .get_phrase("no_data_found") .'</h3>';
            }
            return $output;
        }  
        

        function search($search){
            
            $ci =& get_instance();
            $ci->load->model('admin/categories_model');
            
            $this->db->select("*");	
            $this->db->like('title',$search);
            $this->db->from('services');
            $query = $this->db->get();

            $output = '';
            if($query->num_rows() > 0)
            {
                foreach($query->result_array() as $row)
                {
                    $category_title = $ci->categories_model->get($row['category_id']);
                    $ct_name = $category_title->name;

                    $output .= '
                        <div class="col-xxl-6 col-xl-6 col-lg-6 col-md-6">
                           <div class="product__item white-bg mb-5">
                              <div class="product__thumb">
                                 <div class="product__thumb-inner fix w-img">
                                    <a href="'.base_url().'service/'. sanitizer($row['slug']) .'">
                                    <img src="'.base_url().'public/uploads/services/'. sanitizer($row['imagelocation']) .'" alt="">
                                    </a>
                                 </div>
                              </div>
                              <div class="product__content">
                                 <div class="product__meta mb-4 d-flex justify-content-between align-items-center">
                                    <div class="product__tag">
                                       <a>  
                                        ' . sanitizer($ct_name) . '
                                        </a>
                                    </div>
                                    <div class="product__price">
                                       <span>' . price_currency_format(sanitizer($row['price']), $this->payment_settings->default_currency) .'</span>
                                    </div>
                                 </div>
                                 <h3 class="product__title mb-2">
                                    <a href="'.base_url().'service/'. sanitizer($row['slug']) .'">'. sanitizer($row['title']) .'</a>
                                 </h3>
                                  <span class="time_tag">'. sanitizer($row['time_duration']) .'</span>
                                  <hr>
                                <div class="add-cart">
                                     ' . form_open(base_url('save/cart') ) .'
                                        <input type="hidden" class="buyfield" name="service_id" value="'. sanitizer($row['id']) .'"/>

                                        <input type="number" class="buyfield"  min="1" max="10000"
                                        style=" width: 25%; padding: 0.375rem 0.75rem; font-size: 1rem; line-height: 1.5; color: #495057; background-color: #fff; background-clip: padding-box; border: 1px solid #ced4da; border-radius: 0.25rem; transition: border-color .15s ease-in-out,box-shadow .15s ease-in-out; "
                                        name="qty" value="1" placeholder="Quanty"/>

                                        <input type="submit" class="btn btn-primary btn-sm text-white align-items-center ml-1" name="submit" value="' .get_phrase("add_to_cart") .'"/>
                                    ' . form_close() . '				
                                </div>

                              </div>
                           </div>
                        </div><!--/ col-lg-6 -->  
                    ';
                }
            }
            else
            {
                $output = '<h3>' .get_phrase("no_data_found") .'</h3>';
            }
            return $output;
        }        

    }

?>
