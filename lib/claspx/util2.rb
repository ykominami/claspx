require 'pathname'

module Claspx
  class Util2
    @output_fname = "b.txt"

    class << self
      def count_error_all(label, enum, &block)
        all_counter = 0
        error_counter = 0
        return_array = []
        if enum.instance_of?(Hash)
          enum.each { |k,v|
            all_counter += 1
            result = block.call(k, v)
            #  v.reform_to_camelcase_js_script()
            error_counter += 1 if result.error?
            return_array << result
          }
        elsif enum.instance_of?(Array)
          enum.each { |item|
            all_counter += 1
            result = block.call(item)
            #  v.reform_to_camelcase_js_script()
            error_counter += 1 if result.error?
            return_array << result
          }
        else
          raise
        end
        status = true
        status = false if error_counter > 0
        # result = Result.new(status, "Project#reform_to_camelcase_js_script_all")
        result = Result.new(status, label)
        result.add_return_array(return_array)  
      end
    end
  end
end
