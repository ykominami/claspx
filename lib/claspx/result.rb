module Claspx
  class Result
    attr_reader :status, :message, :std_out, :std_err, :array, :return_value, :return_array, :message_memo
    def initialize(status, message)
      @status = status
      @message = message
      @message_memo = nil
      @std_out = nil
      @std_err = nil
      @array = nil
      @return_value = nil
      @return_array = nil
    end

    def add_message_memo(memo)
      @message_memo = memo

      self
    end

    def add_std_out_and_err(out, err)
      @std_out = out
      @std_err = err

      self
    end

    def add_return_value(value)
      @return_value = value

      self
    end

    def add_return_array(array)
      @return_array = array

      self
    end

    def add_array(array)
      @array = array

      self
    end

    def success?
      @status
    end

    def error?
      @status == false
    end
  end
end